<?php

declare(strict_types=1);

namespace Astral\Serialize\OpenApi\Collections;

use Astral\Serialize\Enums\TypeKindEnum;
use Astral\Serialize\OpenApi\Enum\ParameterTypeEnum;
use Astral\Serialize\Support\Collections\TypeCollection;
use Attribute;

class ParameterCollection
{
    public function __construct(
        public string $className,
        public string       $name,
        /** @var TypeCollection[] $types */
        public array $types,
        public ParameterTypeEnum $type,
        public string       $descriptions = '',
        public mixed        $example = '',
        public bool         $required = false,
        public bool         $ignore = false,
        /** @var array<ParameterCollection[]> $children */
        public array        $children  = [],
    ){
    }
}

