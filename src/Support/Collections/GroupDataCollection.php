<?php

namespace Astral\Serialize\Support\Collections;

/**
 * @template T
 */
class GroupDataCollection
{
    public function __construct(
        private readonly array  $defaultGroups,
        /** @var class-string<T> $className */
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

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getDefaultGroups(): array
    {
        return $this->defaultGroups;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getPropertiesName(): array
    {
        return array_map(fn ($property) => $property->getName(), $this->properties);
    }

    public function getPropertiesInputNamesByGroups(array $groups, string $defaultGroup): array
    {
        $inputNames = [];
        foreach ($this->properties as $property) {
            $inputNames = array_merge($inputNames, $property->getInputNamesByGroups($groups, $defaultGroup));
        }
        return array_values(array_unique($inputNames));
    }


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
}
