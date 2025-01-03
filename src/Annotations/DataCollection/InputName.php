<?php

declare(strict_types=1);

namespace Astral\Serialize\Annotations\DataCollection;

use Astral\Serialize\Contracts\Attribute\DataCollectionCastInterface;
use Astral\Serialize\Support\Collections\DataCollection;
use Attribute;
use ReflectionProperty;

/**
 * 映射前端的属性名称到后端的属性名称
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_CLASS)]
class InputName implements DataCollectionCastInterface
{
    public function __construct(public readonly string $name, public array|string $groups = [])
    {
        $this->groups = is_string($groups) ? (array)$groups : $groups;
    }

    public function resolve(DataCollection $dataCollection, ReflectionProperty|null $property = null): void
    {
        $dataCollection->addInputName($this->name, $this->groups ?: null);
    }
}
