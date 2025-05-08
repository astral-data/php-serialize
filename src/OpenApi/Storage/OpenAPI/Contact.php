<?php

declare(strict_types=1);

namespace Astral\Serialize\OpenApi\Storage\OpenAPI;

use Astral\Serialize\OpenApi\Storage\StorageInterface;

/**
 * 文档开发者介绍
 */
class Contact implements StorageInterface
{
    public function __construct(
        public string $name,
        public string $url,
        public string $email
    ) {
    }
}
