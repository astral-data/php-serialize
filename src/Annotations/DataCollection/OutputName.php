<?php

declare(strict_types=1);

namespace Astral\Serialize\Annotations\DataCollection;

use Astral\Serialize\Contracts\Attribute\DataCollectionCastInterface;
use Astral\Serialize\Contracts\Mappers\NameMapper;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Factories\MapperFactory;
use Attribute;
use ReflectionProperty;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class OutputName implements DataCollectionCastInterface
{
    public function __construct(public string|NameMapper $name, public array|string $groups = [])
    {
        $this->groups = is_string($groups) ? (array)$groups : $groups;
    }

    public function resolve(DataCollection $dataCollection, ReflectionProperty|null $property = null): void
    {
        if (is_subclass_of($this->name, NameMapper::class)) {
            $this->name = MapperFactory::build($this->name)->resolve($dataCollection->getName());
        }

        $dataCollection->addOutName($this->name, $this->groups ?: null);
    }
}
