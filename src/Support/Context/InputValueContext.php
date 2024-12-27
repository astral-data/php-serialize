<?php

namespace Astral\Serialize\Support\Context;

use Astral\Serialize\Resolvers\PropertyInputValueResolver;

class InputValueContext
{
    public function __construct(
        public readonly string $className,
        public readonly object $classInstance,
        public readonly array $payload,
        public readonly PropertyInputValueResolver $propertyInputValueResolver,
    ) {

    }
}
