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
        public array|stdClass $variables = new stdClass(),
    ) {

    }

    public function addVariable(string $name, $description, $default = ''): static
    {
        $this->variables  = $this->variables instanceof stdClass ? [] : $this->variables;

        $this->variables[$name] = ['default' => $default, 'description'=> $description];

        return $this;
    }
}
