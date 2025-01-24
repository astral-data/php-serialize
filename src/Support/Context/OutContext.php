<?php

namespace Astral\Serialize\Support\Context;

use Astral\Serialize\Resolvers\OutputResolver;

class OutContext
{
    public function __construct(
        public readonly string                 $className,
        public readonly object                 $classInstance,
        public readonly OutputResolver         $propertyToArrayResolver,
        public readonly ChooseSerializeContext $chooseSerializeContext,
    ) {

    }
}
