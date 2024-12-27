<?php

namespace Astral\Serialize\Support\Collections;

class ConstructDataCollection
{
    //    private array $tranFromResolvers = [];

    public function __construct(
        public readonly string              $name,
        public readonly bool                $isPromoted,
        public readonly bool                $isOptional,
        public readonly bool                $isNull,
        public readonly mixed               $defaultValue,
    ) {

    }
}
