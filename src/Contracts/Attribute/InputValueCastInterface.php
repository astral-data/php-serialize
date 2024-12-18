<?php

namespace Astral\Serialize\Contracts\Attribute;

use ReflectionProperty;
use Astral\Serialize\Support\Collections\DataCollection;

interface InputValueCastInterface
{

    public function match(mixed $value, DataCollection $collection): bool;
    public function resolve(mixed $value, DataCollection $collection): mixed;
}
