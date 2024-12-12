<?php

namespace Astral\Serialize\Contracts\Attribute;

use Astral\Serialize\Support\Collections\DataCollection;

interface InputValueCastInterface
{
    public function resolve(DataCollection $dataCollection, mixed $value): mixed;
}
