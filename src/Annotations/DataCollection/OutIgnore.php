<?php

declare(strict_types=1);

namespace Astral\Serialize\Annotations\DataCollection;

use ReflectionProperty;
use Astral\Serialize\Contracts\Attribute\DataCollectionCastInterface;
use Astral\Serialize\Support\Collections\DataCollection;
use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class OutIgnore implements DataCollectionCastInterface
{
    public array $groups;

    public function __construct(array|string $groups = [])
    {
        $this->groups = $groups;
    }

    public function resolve(DataCollection $dataCollection, ReflectionProperty|null $property = null): void
    {
        if(!$this->groups || in_array($dataCollection->getParentGroupCollection()->getGroupName(), $this->groups)) {
            $dataCollection->setOutIgnore(true);
        }
    }
}
