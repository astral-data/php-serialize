<?php

declare(strict_types=1);

namespace Astral\Serialize\OpenApi\Annotations;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class OpenApi
{
    public function __construct(
        public string $description = '',
        public string $example = '',
    ) {
    }
}
