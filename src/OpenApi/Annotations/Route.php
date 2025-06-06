<?php

declare(strict_types=1);

namespace Astral\Serialize\OpenApi\Annotations;

use Astral\Serialize\OpenApi\Enum\MethodEnum;
use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Route
{
    public function __construct(
        public string $route,
        public MethodEnum $method = MethodEnum::POST,
        public array $attributes = [],
    ) {
    }
}
