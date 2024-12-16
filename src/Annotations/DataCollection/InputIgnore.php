<?php

declare(strict_types=1);

namespace Astral\Serialize\Annotations\DataCollection;

use Astral\Serialize\Contracts\Attribute\DataCollectionCastInterface;
use Astral\Serialize\Support\Collections\DataCollection;
use Attribute;
use ReflectionProperty;
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

    private function shouldIgnore(string $groupName): bool
    {
        return in_array($groupName, $this->groups, true);
    }

    public function resolve(DataCollection $dataCollection, ReflectionProperty|null $property = null): void
    {
        if ($this->shouldIgnore($dataCollection->getParentGroupCollection()->getGroupName())) {
            $dataCollection->setInputIgnore(true);
        }
    }
}
