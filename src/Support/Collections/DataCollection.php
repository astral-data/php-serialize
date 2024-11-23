<?php

namespace Astral\Serialize\Support\Collections;

use ReflectionProperty;
use Illuminate\Support\Collection;
use Astral\Serialize\Enums\TypeKindEnum;
use Astral\Serialize\Support\Collections\TypeCollection;
use Astral\Serialize\Support\Collections\DataGroupCollection;

class DataCollection
{
    public string $name;

    /** @var TypeCollection[] */
    public array $type;

    public mixed $defaultValue;

    public bool $nullable = true;

    //    public string $inputName;

    //    public bool $inputIgnore = false;

    //    public bool $existSetter = false;
    public array $inputTranFromCollections = [];

    //    public string $outName;

    //    public bool $outIgnore = false;

    //    public bool $existGetter = false;
    public array $outTranFromCollections = [];

    public string $propertyAliasName;

    /** @var DataGroupCollection[]|null */
    public ?array $children = [];

    public  function __construct(string $name, bool $nullable, mixed $defaultValue)
    {
        $this->name = $name;
        $this->defaultValue = $defaultValue;
        $this->nullable = $nullable;
    }

    public function setType(TypeCollection ...$types): void
    {
        $this->type = $types;
    }

    /**
     *
     * @param DataGroupCollection|null $collection
     * @return void
     */
    public  function addChildren(?DataGroupCollection $collection): void
    {
        $this->children[] = &$collection;
    }
}
