<?php

declare(strict_types=1);

namespace Astral\Serialize\OpenApi\Storage\OpenAPI;

use Astral\Serialize\OpenApi\Collections\OpenApiCollection;
use Astral\Serialize\OpenApi\Storage\OpenAPI\Method\MethodInterface;
use Astral\Serialize\OpenApi\Storage\StorageInterface;

class OpenAPI implements StorageInterface
{

    public string $openapi = '3.1.1';

    public ApiInfo $info;

    /** @var ServersStorage[] */
    public array $servers;

    public string $host;

    public string $basePath;

    /** @var array<TagStorage>  */
    public array $tags;

    /** @var array<string,MethodInterface>  */
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

    public function addPath(OpenApiCollection $openApiCollection): self
    {
        $this->paths[$openApiCollection->route->route][strtolower($openApiCollection->route->method->name)] = $openApiCollection->build();
        return $this;
    }


}
