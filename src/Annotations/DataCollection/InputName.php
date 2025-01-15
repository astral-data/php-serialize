<?php

declare(strict_types=1);

namespace Astral\Serialize\Annotations\DataCollection;

use Astral\Serialize\Contracts\Mappers\NameMapper;
use Astral\Serialize\Contracts\Attribute\DataCollectionCastInterface;
use Astral\Serialize\Support\Collections\DataCollection;
use Attribute;
use ReflectionProperty;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_CLASS)]
class InputName implements DataCollectionCastInterface
{
    public function __construct(public string|NameMapper $name, public array|string $groups = [])
    {
        $this->groups = is_string($groups) ? (array)$groups : $groups;
    }

    public function resolve(DataCollection $dataCollection, ReflectionProperty|null $property = null): void
    {
        if ($this->name instanceof NameMapper) {
            $this->name = $this->name->resolve($this->name);
        }

        $dataCollection->addInputName($this->name, $this->groups ?: null);
    }
}
