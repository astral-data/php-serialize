<?php

namespace Astral\Serialize\Contracts\Attribute;

use Astral\Serialize\Support\Collections\DataCollection;

interface InputValueCastInterface
{
    public function resolve(mixed $value, DataCollection $dataCollection): mixed;
}
