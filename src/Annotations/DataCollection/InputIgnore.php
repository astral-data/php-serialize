<?php

declare(strict_types=1);

namespace Astral\Serialize\Annotations\DataCollection;

use ReflectionProperty;
use Astral\Serialize\Contracts\Attribute\DataCollectionCastInterface;
use Astral\Serialize\Support\Collections\DataCollection;
use Attribute;
use UnitEnum;

#[Attribute(Attribute::TARGET_PROPERTY)]
class InputIgnore implements DataCollectionCastInterface
{
    public array $groups;

    public function __construct(string|int|UnitEnum ...$groups)
    {
        $this->$groups = array_map(function ($name) {
            if ($name instanceof UnitEnum) {
                return $name->name;
            }
            return (string) $name;
        }, $groups);
    }

    public function resolve(DataCollection $dataCollection, ReflectionProperty|null $property = null): void
    {
        if(in_array($dataCollection->getParentGroupCollection()->getGroupName(), $this->groups)) {
            $dataCollection->setInputIgnore(true);
        }
    }
}
