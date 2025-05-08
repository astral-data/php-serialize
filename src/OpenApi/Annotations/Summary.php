<?php

declare(strict_types=1);

namespace Astral\Serialize\OpenApi\Annotations;

use Attribute;

/**
 * 方法注解类
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Summary
{
    public function __construct(
        public string $value,
        public string $description = ''
    ) {

    }
}
