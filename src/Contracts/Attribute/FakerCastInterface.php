<?php

namespace Astral\Serialize\Contracts\Attribute;

use Astral\Serialize\Support\Collections\DataCollection;

interface FakerCastInterface
{
    public function resolve(DataCollection $collection): mixed;
}
