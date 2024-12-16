<?php

declare(strict_types=1);

namespace Astral\Serialize\Annotations\DataCollection;

use Astral\Serialize\Contracts\Attribute\DataCollectionCastInterface;
use Astral\Serialize\Support\Collections\DataCollection;
use Attribute;
use ReflectionProperty;

#[Attribute(Attribute::TARGET_PROPERTY)]
class OutIgnore implements DataCollectionCastInterface
{
    public array $groups;

    public function __construct(array|string $groups = [])
    {
        $this->groups = $groups;
    }

    private function shouldIgnore(string $groupName): bool
    {
        return in_array($groupName, $this->groups, true);
    }

    public function resolve(DataCollection $dataCollection, ReflectionProperty|null $property = null): void
    {
        if ($this->shouldIgnore($dataCollection->getParentGroupCollection()->getGroupName())) {
            $dataCollection->setOutIgnore(true);
        }
    }
}
