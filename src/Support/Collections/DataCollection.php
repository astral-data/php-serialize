<?php

namespace Astral\Serialize\Support\Collections;

use InvalidArgumentException;
use ReflectionProperty;

class DataCollection
{
    //    private array $tranFromResolvers = [];

    public function __construct(
        private readonly array               $groups,
        private readonly GroupDataCollection $parentGroupCollection,
        private readonly string              $name,
        private readonly bool                $isNullable,
        private readonly bool                $isReadonly,
        private readonly array              $attributes,
        private readonly mixed              $defaultValue,
        private readonly ReflectionProperty $property,
        /** @var TypeCollection[] */
        private array                       $types = [],
        /** @var array<string,array> */
        private array                       $inputNames = [],
        /** @var array<string,array> */
        private array                       $outNames = [],
        /** @var string[] */
        private array                       $inputIgnoreGroups = [],
        /** @var string[] */
        private array                       $outIgnoreGroups = [],
        /** @var array<class-string,GroupDataCollection> */
        public array                        $children = [],
        private ?string                     $chooseInputName = null,
        private ?string                     $chooseOutputName = null,
        private ?TypeCollection             $chooseType = null,
        //        private ?string                      $propertyAliasName = null,
    ) {
        $this->addInputName($this->name, [$this->parentGroupCollection->getClassName()]);
        $this->addOutName($this->name);
    }

    public function getGroups(): array
    {
        return $this->groups;
    }

    public function getDefaultValue(): mixed
    {
        return $this->defaultValue;
    }

    public function isReadonly(): bool
    {
        return $this->isReadonly;
    }

    public function isNullable(): bool
    {
        return $this->isNullable;
    }

    public function getProperty(): ReflectionProperty
    {
        return $this->property;
    }

    public function getOutNames(): array
    {
        return $this->outNames;
    }

    /**
     * @return array<class-string,GroupDataCollection>
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function getParentGroupCollection(): GroupDataCollection
    {
        return $this->parentGroupCollection;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getInputNames(): array
    {
        return $this->inputNames;
    }

    public function getInputNamesByGroups(array $groups): array
    {
        $vols = [];
        foreach ($groups as $group) {
            $vols =  isset($this->inputNames[$group]) ? array_merge($vols, $this->inputNames[$group]) : $vols;
        }

        return $vols;
    }

    public function getInputIgnoreGroups(): array
    {
        return $this->inputIgnoreGroups;
    }

    public function isInputIgnoreByGroups(array $groups): bool
    {
        foreach ($groups as $group) {
            if (in_array($group, $this->inputIgnoreGroups)) {
                return true;
            }
        }

        return false;
    }

    public function setOutIgnoreGroups(array $vols): self
    {
        $this->outIgnoreGroups = array_merge($this->outIgnoreGroups, $vols);

        return $this;
    }

    public function getOutIgnoreGroups(): array
    {
        return $this->outIgnoreGroups;
    }

    public function setInputIgnoreGroups(array $vols): self
    {
        $this->inputIgnoreGroups = array_merge($this->inputIgnoreGroups, $vols);
        return $this;
    }

    public function addInputName($name, array $groups): self
    {

        foreach ($groups as $group) {
            $this->inputNames[$group][$name] ??= $name;
        }

        return $this;
    }

    public function addOutName($name): self
    {
        $this->outNames[$name] = $name;

        return $this;
    }

    /**
     *
     * @return TypeCollection[]
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    public function setTypes(TypeCollection ...$types): void
    {
        $this->types = $types;
    }

    public function getTypeTo(string $className): TypeCollection
    {
        foreach ($this->types as $typeCollection) {
            if ($typeCollection->className === $className) {
                return $typeCollection;
            }
        }

        throw new InvalidArgumentException("TypeCollection with className '$className' not found.");

    }

    /**
     *
     * @param GroupDataCollection|GroupDataCollection[]|null $collection
     * @return void
     */
    public function addChildren(GroupDataCollection|array|null $collection): void
    {
        if (is_array($collection)) {
            foreach ($collection as $child) {
                if ($child instanceof GroupDataCollection) {
                    $this->addSingleChildren($child);
                }
            }
        } elseif ($collection instanceof GroupDataCollection) {
            $this->addSingleChildren($collection);
        }
    }

    /**
     *
     * @param GroupDataCollection $collection
     * @return void
     */
    private function addSingleChildren(GroupDataCollection $collection): void
    {
        $key = $collection->getClassName();
        if (!isset($this->children[$key])) {
            $this->children[$key] = $collection;
        }
    }

    public function toArray(): array
    {
        return [
            'name'         => $this->name,
            'type'         => array_map(fn ($type) => $type->toArray(), $this->types),
            'defaultValue' => $this->defaultValue,
            'nullable'     => $this->isNullable,
            'children'     => $this->children ? array_map(fn ($child) => $child->toArray(), $this->children) : null,
        ];
    }

    public function getChooseInputName(): string
    {
        return $this->chooseInputName;
    }

    public function setChooseInputName(string $chooseInputName): void
    {
        $this->chooseInputName = $chooseInputName;
    }

    public function getChooseOutputName(): string
    {
        return $this->chooseOutputName;
    }

    public function setChooseOutputName(string $chooseOutputName): void
    {
        $this->chooseOutputName = $chooseOutputName;
    }

    public function getChooseType(): ?TypeCollection
    {
        return $this->chooseType ?? null;
    }

    public function setChooseType(TypeCollection $chooseType): void
    {
        $this->chooseType = $chooseType;
    }
}
