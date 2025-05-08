<?php

declare(strict_types=1);

namespace Astral\Serialize\OpenApi\Handler;

use Astral\Serialize\OpenApi\Collections\OpenApiCollection;
use Astral\Serialize\OpenApi\Storage\OpenAPI\OpenAPI;
use Astral\Serialize\OpenApi\Storage\OpenAPI\ParameterStorage;
use Astral\Serialize\OpenApi\Storage\OpenAPI\SchemaStorage;
use Exception;
use JsonException;
use ReflectionException;

abstract class Handler implements HandleInterface
{
    /** @var OpenAPI */
    protected static OpenAPI $OpenAPI;
    private string $controllerPrefix = '';
    private string $controllerSuffix = '';

    public function __construct(
        protected readonly ParameterStorage $headerParameterStorages = new ParameterStorage()
    ) {
        self::$OpenAPI ?: self::$OpenAPI = new OpenAPI();
    }

    /**
     * 构建OpenApi结构文档
     *
     * @param class-string $className
     * @throws ReflectionException
     * @throws Exception
     */
    abstract public function createOpenAPIByClass(string $className): void;

    /**
     * 向全局头部参数存储中添加一个新的头部参数。
     *
     * @param string $name  参数的名称。
     * @param string $example  参数的示例值，默认为空字符串。
     * @param string $description  参数的描述，默认为空字符串。
     * @return self 返回对象自身，支持链式调用。
     */
    public function addGlobalHeader(string $name, string $example = '', string $description = ''): self
    {
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

    public function handleByAutoLoad(): void
    {
        $classMap    = require base_path('vendor/composer/autoload_classmap.php');
        $appClassMap = array_keys(array_filter($classMap, function ($key) {
            return (str_starts_with($key, 'App\\') && str_ends_with($key, 'Controller'))
                   || (str_starts_with($key, 'April\\') && str_ends_with($key, 'Controller'));
        }, ARRAY_FILTER_USE_KEY));

        foreach ($appClassMap as $className) {
            $this->createOpenAPIByClass($className);
        }
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

                $path = $folder . '/' . $file;
                if ($file == '.' || $file == '..' || strpos($file, '.') === 0) {
                    continue;
                }

                if (is_dir($path)) {
                    $this->handleByFolders([$path => $namespace . '\\' . $file]);

                    continue;
                }

                if (pathinfo($file, PATHINFO_EXTENSION) !== 'php') {
                    continue;
                }

                $fileName  = $this->controllerPrefix . trim(substr($file, 0, strpos($file, '.'))) . $this->controllerSuffix;
                $className = $namespace ? $namespace . '\\' . $fileName : $fileName;

                if (! class_exists($className)) {
                    include_once $folder . '/' . $file;
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
     * 根据类信息构建Schema
     * @throws Exception
     */
    public function buildSchemaByClass(string $className, ?string $group = null): SchemaStorage
    {
        $schema = new SchemaStorage();

        if (! $className) {
            return $schema;
        }

        $ParserPartaker = new ParserPartaker();
        $ParserPartaker->addNode($className, $group, null);

        $tree = $ParserPartaker->getTree();

        $schema->createTree($tree->getChildren());

        return $schema;
    }

    public function output(string $path): bool
    {
        return true;
    }

    /**
     * @throws JsonException
     */
    public function toString(): string
    {
        return json_encode(self::$OpenAPI, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }
}
