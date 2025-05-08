<?php

declare(strict_types=1);

namespace Astral\Serialize\OpenApi\Annotations;

use Astral\Serialize\OpenApi\Storage\OpenAPI\TagStorage;
use Attribute;

/**
 * 栏目注解类
 */
#[Attribute(Attribute::TARGET_CLASS)]
class Tag
{
    public function __construct(
        public string $value,
        public string $description = ''
    ) {
    }

    public function buildTagStorage(): TagStorage
    {
        return new TagStorage($this->value, $this->description);
    }
}
