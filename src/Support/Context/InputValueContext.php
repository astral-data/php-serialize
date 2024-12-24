<?php

namespace Astral\Serialize\Support\Context;

use Astral\Serialize\Resolvers\PropertyInputValueResolver;

class InputValueContext
{
    public function __construct(
        public readonly object $currentObject,
        public readonly array $payload,
        public readonly PropertyInputValueResolver $propertyInputValueResolver,
    ) {

    }
}
