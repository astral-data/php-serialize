<?php

namespace Astral\Serialize\Contracts\Attribute;

use Astral\Serialize\Support\Collections\DataCollection;

interface DataCollectionCastInterface
{
    public function resolve(mixed $cast, DataCollection $dataCollection): void;
}