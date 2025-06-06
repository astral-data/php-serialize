<?php

declare(strict_types=1);


class LaravelDocApi implements DiverterInterface
{
    /** @var OpenAPI */
    private static $OpenAPI;

    /** @var string 类前缀 */
    private string $controllerPrefix = '';

    /** @var string 类后缀 */
    private string $controllerSuffix = '';

    /** @var bool 是否忽略注解异常 默认false */
    private bool $_isIgnoreException = false;

    /** @var ParameterStorage 统一header信息 */
    private $headerParameterStorages;

    public function __construct()
    {
        self::$OpenAPI ?: self::$OpenAPI = new OpenAPI();
    }

    /**
     * 向全局头部参数存储中添加一个新的头部参数。
     *
     * @param  string  $name  参数的名称。
     * @param  string  $example  参数的示例值，默认为空字符串。
     * @param  string  $description  参数的描述，默认为空字符串。
     * @return self 返回对象自身，支持链式调用。
     */
    public function addGlobalHeader($name, $example = '', $description = ''): self
    {
        // 如果头部参数存储尚未初始化，则进行初始化
        if (! $this->headerParameterStorages) {
            $this->headerParameterStorages = new ParameterStorage();
        }

        // 添加头部参数的属性
        $this->headerParameterStorages->addHeaderProperties($name, $description, $example);

        return $this;
    }

    /**
     * Undocumented function
     */
    public function getOpenAPI(): OpenAPI
    {
        return self::$OpenAPI;
    }

    /**
     * 增加类前缀标识
     *
     * @return $this
     */
    public function withControllerPrefix(string $value): self
    {
        $this->controllerPrefix = $value;

        return $this;
    }

    /**
     * 增加类后缀标识
     *
     * @return $this
     */
    public function withControllerSuffix(string $value): self
    {
        $this->controllerSuffix = $value;

        return $this;
    }

    /**
     * 是否开启注解异常信息
     *
     * @return $this
     */
    public function enableException(bool $bool = true): self
    {
        $this->_isIgnoreException = $bool;

        return $this;
    }

    /**
     * 解析Controller文件
     *
     * @param  array<string,string>  $folders  文件路径 => 命名空间
     * @return $this
     */
    public function handleByFolders(array $folders): self
    {

        foreach ($folders as $folder => $namespace) {

            if (! is_dir($folder)) {
                continue;
            }

            foreach (scandir($folder) as $file) {

                $path = $folder.'/'.$file;
                if ($file == '.' || $file == '..' || strpos($file, '.') === 0) {
                    continue;
                }

                if (is_dir($path)) {
                    $this->handleByFolders([$path => $namespace.'\\'.$file]);

                    continue;
                }

                if (pathinfo($file, PATHINFO_EXTENSION) != 'php') {
                    continue;
                }

                $fileName = $this->controllerPrefix.trim(substr($file, 0, strpos($file, '.'))).$this->controllerSuffix;
                $className = $namespace ? $namespace.'\\'.$fileName : $fileName;

                if (! class_exists($className)) {
                    include_once $folder.'/'.$file;
                    ob_clean(); // 清除一些引入进来的莫名其妙输出文件
                    if (! class_exists($className)) {
                        continue;
                    }
                }

                $this->createOpenAPIByClass($className);
            }
        }

        return $this;
    }

    /**
     * 构建OpenApi结构文档
     *
     * @param  class-string<T>  $className
     */
    public function createOpenAPIByClass($className): void
    {
        // try {

        $classRefection = new ReflectionClass($className);

        $tagDoc = $classRefection->getAttributes(Tag::class);
        /** @var Tag */
        $tagDoc = isset($tagDoc[0]) ? $tagDoc[0]->newInstance() : null;
        if ($tagDoc) {
            self::$OpenAPI->addTag($tagDoc->buildTagStorage());
        }

        foreach ($classRefection->getMethods() as $item) {

            /** @var ReflectionMethod */
            $reflectionMethod = $classRefection->getMethod($item->name);
            $methodAttributes = $reflectionMethod->getAttributes();

            if (! $methodAttributes) {
                continue;
            }

            $routeDoc = null;
            $summaryDoc = null;
            $requestBodyDoc = null;
            $responseDoc = null;
            foreach ($methodAttributes as $methodAttribute) {
                switch ($methodAttribute) {
                    case $methodAttribute->getName() == Route::class:
                        /** @var Route */
                        $routeDoc = $methodAttribute->newInstance();
                        break;
                    case $methodAttribute->getName() == Summary::class:
                        /** @var Summary */
                        $summaryDoc = $methodAttribute->newInstance();
                        break;
                    case $methodAttribute->getName() == RequestBody::class:
                        /** @var RequestBody */
                        $requestBodyDoc = $methodAttribute->newInstance();
                        break;
                    case $methodAttribute->getName() == Response::class:
                        /** @var Response */
                        $responseDoc = $methodAttribute->newInstance();
                        break;
                }
            }

            if (! $routeDoc || ! $summaryDoc) {
                continue;
            }

            $methodClass = $routeDoc->getMethod();
            /** @var MethodInterface|Method|<T> */
            $openAPIMethod = new $methodClass($summaryDoc->value, $summaryDoc->description ?: '', [$tagDoc->value ?: '']);

            // 统一header头
            if ($this->headerParameterStorages) {
                $openAPIMethod->withParameters($this->headerParameterStorages->getData());
            }

            if ($requestBodyDoc) {
                $openAPIRequestBody = new OpenAPIRequestBody($requestBodyDoc->contentType);
                $requestBodySchema = $this->buildSchemaByClass($requestBodyDoc->className);
                $openAPIRequestBody->withParameter($requestBodySchema);
                $openAPIMethod->withRequestBody($openAPIRequestBody);
            } else {
                $methodParam = $reflectionMethod->getParameters();
                if (isset($methodParam[0]) && ($requestBodyClass = $methodParam[0]->gettype()->getName()) !== Request::class) {
                    $openAPIRequestBody = new OpenAPIRequestBody(ContentTypeEnum::JSON);
                    $requestBodySchema = $this->buildSchemaByClass($requestBodyClass);
                    $openAPIRequestBody->withParameter($requestBodySchema);
                    $openAPIMethod->withRequestBody($openAPIRequestBody);
                }
            }

            if ($responseDoc) {
                if (! class_exists($responseDoc->className)) {
                    throw new Exception(
                        sprintf('Class "%s" does not exist in "%s" from action "%s"',
                            $responseDoc->className,
                            $reflectionMethod->getFileName(),
                            $reflectionMethod->getName())
                    );
                }

                $openApiResponse = new OpenAPIResponse();
                $openApiResponse->description = '成功';
                $openApiResponse->withParameter($this->buildSchemaByClass($responseDoc->className));
                $openAPIMethod->addResponse($responseDoc->code, $openApiResponse);

            } else {
                /** @var ReflectionType */
                $returnClass = $classRefection->getMethod($item->name)->getReturnType();
                if ($returnClass && class_exists($returnClass->getName())) {
                    $openApiResponse = new OpenAPIResponse();
                    $openApiResponse->description = '成功';
                    $openApiResponse->withParameter($this->buildSchemaByClass($returnClass->getName()));
                    $openAPIMethod->addResponse(200, $openApiResponse);
                }
            }

            // /** @var Params[] */
            // $paramsDoc = $reflectionMethod->getAttributes(Params::class);
            // if ($paramsDoc) {
            //     $Parameter = new Parameter();
            //     foreach ($paramsDoc as $v) {
            //         $Parameter->addProperties($v->name, $v->type, $v->value, $v->example, $v->required);
            //     }

            //     $openAPIMethod->withParameters($Parameter->getData());
            // }

            // /** @var RequestValue[] */
            // $requestValuesDoc = $reflectionMethod->getAttributes(RequestValue::class);
            // if ($requestValuesDoc && $openAPIRequestBody) {
            //     foreach ($requestValuesDoc as $v) {
            //         $requestBodySchema->addProperties($v->name, $v->type, $v->value, $v->example, $v->required);
            //     }

            //     $openAPIRequestBody->withParameter($requestBodySchema);
            //     $openAPIMethod->withRequestBody($openAPIRequestBody);
            // }

            self::$OpenAPI->addPath($routeDoc->route, $openAPIMethod);
        }
        // } catch (Throwable $th) {
        //     if ($this->_isIgnoreException) {
        //         echo '解析参数异常:'.PHP_EOL;
        //         exit(highlight_string(var_export($th, true)));
        //     }

        // }

    }

    /**
     * 根据类信息构建Schema
     */
    public function buildSchemaByClass(string $className): SchemaStorage
    {
        $schema = new SchemaStorage();

        if (! $className) {
            return $schema;
        }

        $ParserPartaker = new ParserPartaker();
        $ParserPartaker->addNode($className, null);

        $tree = $ParserPartaker->getTree();

        $schema->createTree($tree->getChildren());

        return $schema;
    }

    public function output(string $path): bool
    {
        return true;
    }

    public function toString(): string
    {
        return json_encode(self::$OpenAPI, JSON_UNESCAPED_UNICODE);
    }
}
