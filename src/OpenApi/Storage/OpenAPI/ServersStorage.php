<?php

declare(strict_types=1);

namespace Astral\Serialize\OpenApi\Storage\OpenAPI;

use Astral\Serialize\OpenApi\Storage\StorageInterface;
use stdClass;

class ServersStorage implements StorageInterface
{
    public function __construct(
        public string $url,
        public string $description,
        public array|stdClass|null $variables = null
    ) {
        $this->variables = $this->variables ?: new stdClass();
    }
}
