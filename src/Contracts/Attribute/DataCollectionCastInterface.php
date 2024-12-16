<?php

namespace Astral\Serialize\Contracts\Attribute;

use Astral\Serialize\Support\Collections\DataCollection;
use ReflectionProperty;

interface DataCollectionCastInterface
{
    public function resolve(DataCollection $dataCollection, ReflectionProperty|null $property = null): void;
}
