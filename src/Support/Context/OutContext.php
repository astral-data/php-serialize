<?php

namespace Astral\Serialize\Support\Context;

use Astral\Serialize\Resolvers\PropertyToArrayResolver;

class OutContext
{
    public function __construct(
        public readonly string                  $className,
        public readonly object $classInstance,
        public readonly PropertyToArrayResolver $propertyToArrayResolver,
        public readonly ChooseSerializeContext  $chooseSerializeContext,
    ) {

    }
}
