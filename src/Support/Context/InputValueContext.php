<?php

namespace Astral\Serialize\Support\Context;

use Astral\Serialize\Resolvers\InputResolver;

class InputValueContext
{
    public function __construct(
        public readonly string                 $className,
        public readonly object                 $classInstance,
        public readonly array                  $payload,
        public readonly InputResolver          $propertyInputValueResolver,
        public readonly ChooseSerializeContext $chooseSerializeContext,
    ) {

    }
}
