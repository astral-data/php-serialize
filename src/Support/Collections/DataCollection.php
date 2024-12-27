<?php

namespace Astral\Serialize\Support\Collections;

use InvalidArgumentException;
use ReflectionProperty;

class DataCollection
{
    //    private array $tranFromResolvers = [];

    public function __construct(
        private readonly GroupDataCollection $parentGroupCollection,
        private readonly string              $name,
        private readonly bool                $isNullable,
        private readonly bool                $isReadonly,
        private readonly array               $attributes,
        private readonly mixed               $defaultValue,
        private readonly ReflectionProperty  $property,
        /** @var TypeCollection[] */
        private array                        $types = [],
        private array                        $inputNames = [],
        private array                        $outNames = [],
        private bool                         $inputIgnore = false,
        private bool                         $outIgnore = false,
        /** @var array<class-string,GroupDataCollection> */
        public array                         $children = [],
        private ?string                      $chooseInputName = null,
        private ?string                      $chooseOutputName = null,
        private ?TypeCollection              $chooseType = null,
        //        private ?string                      $propertyAliasName = null,
    ) {
        $this->addInputName($this->name);
        $this->addOutName($this->name);
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
     * 合并另一个 DataCollection
     */
    public function merge(DataCollection $dataCollection): DataCollection
    {
        $cloneDataCollection = clone $this;
        $cloneDataCollection->mergeInputIgnore($dataCollection->inputIgnore);
        $cloneDataCollection->mergeInputName($dataCollection->inputNames);
        $cloneDataCollection->mergeInputTranFromCollections($dataCollection->inputTranFromCollections);
        $cloneDataCollection->mergeOutIgnore($dataCollection->outIgnore);
        $cloneDataCollection->mergeOutName($dataCollection->outNames);
        $cloneDataCollection->mergeOoutTranFromCollections($dataCollection->outTranFromCollections);
        $cloneDataCollection->mergeChildren($dataCollection->children);

        return $cloneDataCollection;
    }

    public function mergeChildren(GroupDataCollection $dataGroupCollection)
    {
        $children = [];
        foreach ($this->getChildren() as $item) {

        }

        //        return $dataGroupCollection->merge($dataGroupCollection->)
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

    public function getInputIgnore(): bool
    {
        return $this->inputIgnore;
    }

    public function setOutIgnore(bool $val): self
    {
        $this->outIgnore = $val;
        return $this;
    }

    public function getOutIgnore(): bool
    {
        return $this->outIgnore;
    }

    public function setInputIgnore(bool $val): self
    {
        $this->inputIgnore = $val;
        return $this;
    }

    public function addInputName($name): self
    {
        $this->inputNames[$name] = $name;

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
