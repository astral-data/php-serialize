<?php

namespace Astral\Serialize\Contracts\Attribute;

use ReflectionProperty;
use Astral\Serialize\Support\Collections\DataCollection;

interface DataCollectionCastInterface
{
    public function resolve(DataCollection $dataCollection, ReflectionProperty|null $property = null): void;
}