<?php

declare(strict_types=1);

namespace Astral\Serialize\OpenApi\Annotations;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Tag
{
    public function __construct(
        public string $value,
        public string $description = '',
        public int $sort = 0,
    ) {
    }
}
