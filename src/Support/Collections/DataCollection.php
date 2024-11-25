<?php

namespace Astral\Serialize\Support\Collections;

class DataCollection
{
    private string $name;

    /** @var TypeCollection[] */
    private array $type;

    private mixed $defaultValue;

    private bool $nullable = true;

    //    public string $inputName;

    //    public bool $inputIgnore = false;

    //    public bool $existSetter = false;
    private array $inputTranFromCollections = [];

    //    public string $outName;

    //    public bool $outIgnore = false;

    //    public bool $existGetter = false;
    private array $outTranFromCollections = [];

    private string $propertyAliasName;

    /** @var DataGroupCollection[]|null */
    public ?array $children = null;

    public function __construct(string $name, bool $nullable, mixed $defaultValue)
    {
        $this->name         = $name;
        $this->defaultValue = $defaultValue;
        $this->nullable     = $nullable;
    }

    public function getName(): string
    {
        return $this->name;
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
     * @param DataGroupCollection|null $child
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
