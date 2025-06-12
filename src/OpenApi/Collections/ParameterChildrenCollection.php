<?php

namespace Astral\Serialize\OpenApi\Collections;


use Astral\Serialize\OpenApi\Enum\ParameterTypeEnum;

class ParameterChildrenCollection
{
    public function __construct(
        public ParameterTypeEnum $type = ParameterTypeEnum::STRING,
        /** @var array<int, ParameterCollection[]> $children */
        public array             $children = [],
    )
    {
    }
}