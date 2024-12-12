<?php

declare(strict_types=1);

namespace Astral\Serialize\Annotations\DataCollection;

use ReflectionProperty;
use Astral\Serialize\Contracts\Attribute\DataCollectionCastInterface;
use Astral\Serialize\Support\Collections\DataCollection;
use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class PropertyAlisa implements DataCollectionCastInterface
{
    public string $name;

    public array $groups;

    public function __construct(string $name, array|string $groups = [])
    {

        $this->name   = $name;
        $this->groups = is_string($groups) ? (array)$groups : $groups;
    }

    public function resolve(DataCollection $dataCollection, ReflectionProperty|null $property = null): void
    {
        // TODO: Implement resolve() method.
    }
}
