<?php

namespace Astral\Serialize\Support\Collections;

class DataCollection
{
    /** @var TypeCollection[] */
    private array $type;

    private array $inputName = [];

    private array $outName = [];

    private bool $inputIgnore = false;

    private bool $outIgnore = false;

    private array $tranFromResolvers = [];

    private string $propertyAliasName;


    /** @var DataGroupCollection[]|null */
    public ?array $children = null;

    public function __construct(
        private readonly DataGroupCollection $parentGroupCollection,
        private readonly string              $name,
        private readonly bool                $nullable,
        private readonly mixed               $defaultValue,
        private readonly array               $attributes,
    ) {

    }

    /**
     * 合并另一个 DataCollection
     */
    public function merge(DataCollection $dataCollection): DataCollection
    {
        $cloneDataCollection = clone $this;
        $cloneDataCollection->mergeInputIgnore($dataCollection->inputIgnore);
        $cloneDataCollection->mergeInputName($dataCollection->inputName);
        $cloneDataCollection->mergeInputTranFromCollections($dataCollection->inputTranFromCollections);
        $cloneDataCollection->mergeOutIgnore($dataCollection->outIgnore);
        $cloneDataCollection->mergeOutName($dataCollection->outName);
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
     * @return DataGroupCollection[]|null
     */
    public function getChildren(): array|null
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

    public function getInputName(): array
    {
        return $this->inputName;
    }

    public function getInputIgnore(): bool
    {
        return $this->inputIgnore;
    }

    public function setOutIgnore(bool $val): static
    {
        $this->outIgnore = $val;
        return $this;
    }

    public function getOutIgnore(): bool
    {
        return $this->outIgnore;
    }

    public function setInputIgnore(bool $val): static
    {
        $this->inputIgnore = $val;
        return $this;
    }

    public function addInputName($name): static
    {
        $this->inputName[$name] = $name;

        return $this;
    }

    public function addOutName($name): static
    {
        $this->outName[$name] = $name;

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
}
