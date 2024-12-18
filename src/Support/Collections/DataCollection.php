<?php

namespace Astral\Serialize\Support\Collections;

use InvalidArgumentException;

class DataCollection
{
    /** @var TypeCollection[] */
    private array $type;

    private array $inputNames = [];

    private array $outNames = [];

    private string $chooseInputName;

    private string $chooseOutputName;

    private ?TypeCollection $chooseType;

    private bool $inputIgnore = false;

    private bool $outIgnore = false;

    private array $tranFromResolvers = [];

    private string $propertyAliasName;


    /** @var array<class-string,DataGroupCollection> */
    public ?array $children = null;

    public function __construct(
        private readonly DataGroupCollection $parentGroupCollection,
        private readonly string              $name,
        private readonly bool                $nullable,
        private readonly mixed               $defaultValue,
        private readonly array               $attributes,
    ) {
        $this->addInputName($this->name);
        $this->addOutName($this->name);
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

    public function mergeChildren(DataGroupCollection $dataGroupCollection)
    {
        $children = [];
        foreach ($this->getChildren() as $item) {

        }

        //        return $dataGroupCollection->merge($dataGroupCollection->)
    }

    /**
     * @return array<class-string,DataGroupCollection>
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function getParentGroupCollection(): DataGroupCollection
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
    public function getType(): array
    {
        return $this->type;
    }

    public function setType(TypeCollection ...$types): void
    {
        $this->type = $types;
    }

    public function getTypeTo(string $className): TypeCollection
    {
        foreach ($this->type as $typeCollection) {
            if ($typeCollection->className === $className) {
                return $typeCollection;
            }
        }

        throw new InvalidArgumentException("TypeCollection with className '$className' not found.");

    }

    /**
     *
     * @param DataGroupCollection|DataGroupCollection[]|null $collection
     * @return void
     */
    public function addChildren(DataGroupCollection|array|null $collection): void
    {
        if (is_array($collection)) {
            foreach ($collection as $child) {
                if ($child instanceof DataGroupCollection) {
                    $this->addSingleChildren($child);
                }
            }
        } elseif ($collection instanceof DataGroupCollection) {
            $this->addSingleChildren($collection);
        }
    }

    /**
     *
     * @param DataGroupCollection $collection
     * @return void
     */
    private function addSingleChildren(DataGroupCollection $collection): void
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
            'type'         => array_map(fn ($type) => $type->toArray(), $this->type),
            'defaultValue' => $this->defaultValue,
            'nullable'     => $this->nullable,
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
