<?php

declare(strict_types=1);

namespace Astral\Serialize\Cast;

use Astral\Serialize\Contracts\Attribute\OutValueCastInterface;
use Astral\Serialize\Support\Collections\DataCollection;
use UnitEnum;

class OutValueEnumCast implements OutValueCastInterface
{
    public function resolve(DataCollection $dataCollection, mixed $value): string
    {
        if($value instanceof UnitEnum) {
            return $value->name;
        }

        return $value;
    }
}
