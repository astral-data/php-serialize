<?php

declare(strict_types=1);

namespace Astral\Serialize\OpenApi\Handler;

use Astral\Serialize\OpenApi\Storage\OpenAPI\ApiInfo;
use Astral\Serialize\OpenApi\Storage\OpenAPI\OpenAPI;
use Astral\Serialize\OpenApi\Storage\OpenAPI\ParameterStorage;
use Exception;
use JsonException;
use ReflectionException;

abstract class Handler implements HandleInterface
{
    /** @var OpenAPI|null */
    public static ?OpenAPI $openAPI = null;

    public function __construct(
        protected readonly ParameterStorage $headerParameterStorages = new ParameterStorage()
    ) {

        self::$openAPI ??= (new OpenAPI())
            ->withApiInfo(new ApiInfo(Config::get('title'), Config::get('description')))
            ->withServers(Config::get('service'));

        if (Config::has('headers')) {
            foreach (Config::get('headers') as $header) {
                $this->headerParameterStorages->addHeaderProperties($header['name'], $header['description'], $header['example']);
            }
        }
    }

    /**
     * 构建OpenApi结构文档
     *
     * @param class-string $className
     * @throws ReflectionException
     * @throws Exception
     */
    abstract public function buildByClass(string $className): void;

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
     * 遍历整个项目根目录，自动扫描所有 PHP 文件，
     * 如果文件内容中包含 "Astral\Serialize\OpenApi\Annotations"，
     * 则认为它是需要处理的 Controller，进而调用 buildByClass。
     *
     * @return $this
     * @throws ReflectionException
     */
    public function handleByFolders(): self
    {
        $this->scanFolderRecursively(Config::rootPath());
        return $this;
    }

    /**
     * 递归扫描指定目录下的所有子目录和文件。
     * @param string $folder 要扫描的文件夹路径
     * @throws ReflectionException
     */
    protected function scanFolderRecursively(string $folder): void
    {
        // 如果不是目录，跳过
        if (! is_dir($folder)) {
            return;
        }

        $excludeDirs =  array_merge(Config::get('exclude_dirs', []), ['/vendor', '/tests']);

        // 遍历当前文件夹下的所有内容
        foreach (scandir($folder) as $file) {
            // 跳过 . 和 .. ，以及隐藏文件夹/文件
            if ($file === '.' || $file === '..' || str_starts_with($file, '.')) {
                continue;
            }

            $path = $folder . DIRECTORY_SEPARATOR . $file;

            // 如果是子目录，则递归，并拼接命名空间
            if (is_dir($path)) {

                // 检查是否是需要排除的目录
                $relativePath = '/' . ltrim(str_replace(Config::rootPath(), '', $path), '\\/');
                if (in_array($relativePath, $excludeDirs)) {
                    continue;
                }

                $this->scanFolderRecursively($path);
                continue;
            }

            // 只处理 .php 文件
            if (pathinfo($file, PATHINFO_EXTENSION) !== 'php') {
                continue;
            }

            // 读取文件内容，检查是否包含 Annotations 关键字
            $fileContent = @file_get_contents($path);
            if ($fileContent === false) {
                continue;
            }

            // 如果文件中没有引入 Astral\Serialize\OpenApi\Annotations，就跳过
            if (!str_contains($fileContent, 'OpenApi\Annotations')) {
                continue;
            }

            // 计算类名：去掉 .php 之后，将命名空间前缀 + 文件名 组成完整类名
            $baseName  = substr($file, 0, -4); // 去掉 ".php"
            $className =  $this->getNamespaceFromFile($fileContent);
            $className = $className ? $className . '\\' . $baseName : $baseName;

            // 如果类尚未加载，则尝试 include
            if (! class_exists($className)) {
                continue;
            }

            // 调用子类实现的 buildByClass
            $this->buildByClass($className);
        }
    }

    protected function getNamespaceFromFile(string $fileContent): ?string
    {
        if (preg_match('/namespace\s+([^;]+);/', $fileContent, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    /**
     * @throws JsonException
     */
    public function toString(): string
    {
        return json_encode(self::$openAPI, JSON_THROW_ON_ERROR);
    }
}
