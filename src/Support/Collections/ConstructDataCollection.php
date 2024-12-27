<?php

namespace Astral\Serialize\Support\Collections;

use ReflectionProperty;
use InvalidArgumentException;

class ConstructDataCollection
{
    //    private array $tranFromResolvers = [];

    public function __construct(
        public readonly string              $name,
        public readonly bool                $isPromoted,
        public readonly bool                $isOptional,
    ) {

    }
    

}
