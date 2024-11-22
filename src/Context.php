<?php

namespace Astral\Serialize;

use Astral\Serialize\Enums\PropertyKindEnum;
use Astral\Serialize\Support\Caching\GlobalDataCollectionCache;
use Astral\Serialize\Support\Caching\SerializeCollectionCache;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Collections\DataGroupCollection;
use Astral\Serialize\Support\Instance\ReflectionClassInstanceManager;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

class Context
{
    private string $serializeClassName;
    private array $groups;
    private const DEFAULT_GROUP_NAME = 'default';

    public function __construct(string $class, array $groups = [])
    {
        $this->serializeClassName = $class;
        $this->groups = $groups ?: [self::DEFAULT_GROUP_NAME];
    }

    public function setGroups(array $groups): static
    {
        $this->groups = $groups;
        return $this;
    }

    public function getSerializeCollection(): DataGroupCollection
    {
        if (SerializeCollectionCache::has($this->serializeClassName)) {
            return SerializeCollectionCache::get($this->serializeClassName);
        }

        return $this->getGroupCollection();
    }

    public  function getGroupCollection(): DataGroupCollection
    {
        $datas = [];
        foreach ($this->groups as $group) {
            if (!GlobalDataCollectionCache::has($this->serializeClassName, $group)) {
                $this->parseSerializeClass($group);
            }
        }

        return $data;
    }

    /**
     * @throws ReflectionException
     */
    public function parseSerializeClass(string $groupName): ?DataGroupCollection
    {
        $globalDataCollection = new DataGroupCollection();
        $reflectionClass = ReflectionClassInstanceManager::get($this->serializeClassName);
        foreach ($reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $dataCollection = new DataCollection(
                property: $property,
                name: $property->getName(),
                type: $property->getType()->getName(),
                kind: PropertyKindEnum::fromTypeName($property)
            );
            $globalDataCollection->put($dataCollection);
        }
        GlobalDataCollectionCache::put($this->serializeClassName, $groupName, $globalDataCollection);

        return $globalDataCollection;
    }



    public function setPayload(mixed $payload): void {}

    public  function toArray() {}
}
