<?php

namespace Astral\Serialize\Support\Collections;

class GroupDataCollection
{
    public function __construct(
        private readonly array  $defaultGroups,
        private readonly string $className,
        /** @var array<string, ConstructDataCollection> $constructProperties */
        private readonly array  $constructProperties,
        /** @var DataCollection[] */
        private array           $properties = [],
    ) {

    }

    /**
     * @return ConstructDataCollection[]
     */

    public function getConstructProperties(): array
    {
        return $this->constructProperties;
    }

    public function getConstructProperty(string $name): ?ConstructDataCollection
    {
        return $this->constructProperties[$name] ?? null;
    }

    /**
     * 获取所有 DataCollection 属性
     */
    public function getProperties(): array
    {
        return $this->properties;
    }


    public function getDefaultGroups(): array
    {
        return $this->defaultGroups;
    }

    public function getGroupName(): string
    {
        return $this->groupName;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getPropertiesName(): array
    {
        return array_map(fn ($property) => $property->getName(), $this->properties);
    }

    /**
     * 添加一个 DataCollection 属性
     */
    public function put(DataCollection $collection): void
    {
        $key = $collection->getName();

        if (!isset($this->properties[$key])) {
            $this->properties[$key] = $collection;
        }
    }

    public function hasConstruct(): bool
    {
        return !empty($this->constructProperties);
    }

    public function hasConstructProperty(string $name): bool
    {
        return isset($this->constructProperties[$name]);
    }


    public function count(): int
    {
        return count($this->properties);
    }

    public function getPropertyTo(string $inputName): ?DataCollection
    {
        return collect($this->getProperties())->first(fn ($e) => in_array($inputName, $e->inputName)) ?? null;
    }
}
