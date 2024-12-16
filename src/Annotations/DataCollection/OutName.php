<?php

declare(strict_types=1);

namespace Astral\Serialize\Annotations\DataCollection;

use Astral\Serialize\Contracts\Attribute\DataCollectionCastInterface;
use Astral\Serialize\Support\Collections\DataCollection;
use Attribute;
use ReflectionProperty;

/**
 * toArray输出的属性名称
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class OutName implements DataCollectionCastInterface
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
        $dataCollection->addOutName($this->name);
    }
}
