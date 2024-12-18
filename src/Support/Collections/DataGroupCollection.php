<?php

namespace Astral\Serialize\Support\Collections;

class DataGroupCollection
{
    private string $groupName;

    /**
     * @var class-string
     */
    private string $className;

    /** @var DataCollection[] */
    private array $properties = [];

    public function __construct(string $groupName, string $className)
    {
        $this->groupName = $groupName;
        $this->className = $className;
    }

    /**
     * 获取所有 DataCollection 属性
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getGroupName(): string
    {
        return $this->groupName;
    }

    public function getClassName(): string
    {
        return $this->className;
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

    /**
     * 合并另一个 DataGroupCollection
     */
    public function merge(DataGroupCollection $collection): DataGroupCollection
    {
        $cloneCollection = clone $this;
        foreach ($collection->getProperties() as $property) {
            $name = $property->getName();
            if (!isset($cloneCollection->properties[$name])) {
                $cloneCollection->properties[$name] = $property;
            } else {
                $cloneCollection->properties[$name] = $property->merge($property);
            }
        }

        return $cloneCollection;
    }

    public function toArray(): array
    {
        return [
            'groupName'  => $this->groupName,
            'className'  => $this->className,
            'properties' => array_map(fn ($property) => $property->toArray(), $this->properties),
        ];
    }

    public function getPropertiesName(): array
    {
        return array_map(fn ($property) => $property->getName(), $this->properties);
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
