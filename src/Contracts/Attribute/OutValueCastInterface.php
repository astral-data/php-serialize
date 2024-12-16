<?php

namespace Astral\Serialize\Contracts\Attribute;

use Astral\Serialize\Support\Collections\DataCollection;

interface OutValueCastInterface
{
    public function resolve(DataCollection $dataCollection, mixed $value): mixed;
}
