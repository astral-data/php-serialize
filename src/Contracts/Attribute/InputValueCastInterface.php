<?php

namespace Astral\Serialize\Contracts\Attribute;

use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Context\InputValueContext;

interface InputValueCastInterface
{
    public function match(mixed $value, DataCollection $collection, InputValueContext $context): bool;

    public function resolve(mixed $value, DataCollection $collection, InputValueContext $context): mixed;
}
