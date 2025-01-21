<?php

namespace Astral\Serialize\Support\Context;

use Astral\Serialize\Faker\FakerResolver;

class FakerValueContext
{
    public function __construct(
        public readonly string $className,
        public readonly object $classInstance,
        public readonly FakerResolver $propertyInputValueResolver,
    ) {

    }
}
