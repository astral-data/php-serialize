<?php

declare(strict_types=1);

namespace Astral\Serialize\OpenApi\Annotations;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Response
{
    public function __construct(
        /** @var class-string|string $className */
        public string   $className  = '',
        public ?array   $groups     = null,
        public ?int     $code       = 200,
    ) {
    }
}
