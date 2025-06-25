<?php

declare(strict_types=1);

namespace Astral\Serialize\OpenApi\Annotations;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Response
{
    public function __construct(
        /** @var class-string $className */
        public string   $className,
        public ?int     $code       = 200,
        public ?array   $groups     = null,
    ) {
    }
}
