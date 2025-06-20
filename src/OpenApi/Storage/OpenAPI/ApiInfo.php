<?php

declare(strict_types=1);

namespace Astral\Serialize\OpenApi\Storage\OpenAPI;

use Astral\Serialize\OpenApi\Storage\StorageInterface;

/**
 * 接口文档全局参数信息
 */
class ApiInfo implements StorageInterface
{
    public function __construct(
        public string $title,
        public string $description,
        public string $version = '1.0.0'
    ) {
    }
}
