<?php

declare(strict_types=1);

namespace Astral\Serialize\OpenApi\Storage\OpenAPI;

use Astral\Serialize\OpenApi\Storage\OpenAPI\Method\MethodInterface;
use Astral\Serialize\OpenApi\Storage\StorageInterface;

class OpenAPI implements StorageInterface
{
    /** @var string openapi版本 */
    public string $openapi = '3.0.0';

    /** @var ApiInfo openapi文档头信息 */
    public ApiInfo $info;

    /** @var ServersStorage[] 服务器信息 */
    public array $servers;

    /** @var string 请求api的域名 */
    public string $host;

    /** @var string 请求api的标识符 */
    public string $basePath;

    /** @var array<TagStorage> 请求api的标识符 */
    public array $tags;

    /** @var array<string,MethodInterface> 具体api配置 */
    public array $paths = [];

    public function withApiInfo(ApiInfo $apiInfo): self
    {
        $this->info = $apiInfo;
        return $this;
    }

    public function withServers(array $servers): self
    {
        $this->servers = $servers;
        return $this;
    }

    public function withTags(array $tags): self
    {
        $this->tags = $tags;
        return $this;
    }

    public function addTag(TagStorage $tag): void
    {
        $this->tags[] = $tag;
    }

    public function withPaths(array $paths): self
    {
        $this->paths = $paths;
        return $this;
    }

    public function addPath(string $url, MethodInterface $method): self
    {
        $this->paths[$url][$method->getName()] = $method;
        return $this;
    }
}
