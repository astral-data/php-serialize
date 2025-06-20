<?php

declare(strict_types=1);

namespace Astral\Serialize\OpenApi\Storage\OpenAPI;

use Astral\Serialize\OpenApi\Storage\StorageInterface;

class TagStorage implements StorageInterface
{
    public function __construct(
        public string $name,
        public string $description = ''
    ) {
    }
}
