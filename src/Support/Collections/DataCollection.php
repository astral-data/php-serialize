<?php

namespace Astral\Serialize\Support\Collections;

use Astral\Serialize\Enums\PropertyKindEnum;
use Illuminate\Support\Collection;

class DataCollection {

    public string $name;
    public PropertyKindEnum $kind;
    public mixed $type;

//    public string $inputName;
//    public bool $inputIgnore = false;
//    public bool $existSetter = false;
    public array $inputTranFromCollections = [];
//    public string $outName;
//    public bool $outIgnore = false;
//    public bool $existGetter = false;
    public array $outTranFromCollections = [];
    public string $propertyAliasName;

    /** @var DataGroupCollection|null */
    public ?DataGroupCollection $children;

    public  function __construct(string $name,mixed $type, ?PropertyKindEnum $kind)
    {
        $this->name = $name;
        $this->type = $type;
        $this->kind = $kind;
    }

    public  function setChildren(?DataGroupCollection $collection) : void
    {
        $this->children = &$collection;
    }

}