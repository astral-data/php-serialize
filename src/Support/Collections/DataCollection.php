<?php

namespace Astral\Serialize\Support\Collections;

use Astral\Serialize\Enums\TypeKindEnum;
use Illuminate\Support\Collection;
use ReflectionProperty;

class DataCollection
{
<<<<<<< HEAD

    public ReflectionProperty $property;
=======
>>>>>>> 6af8e3a436675df3f65f507f4e1222a71c995b30
    public string $name;

<<<<<<< HEAD
    //    public string $inputName;
    //    public bool $inputIgnore = false;
    //    public bool $existSetter = false;
    public array $inputTranFromCollections = [];
    //    public string $outName;
    //    public bool $outIgnore = false;
=======
    /** @var TypeCollection[] */
    public array $type;

    public mixed $defaultValue;

    //    public string $inputName;

    //    public bool $inputIgnore = false;

    //    public bool $existSetter = false;
    public array $inputTranFromCollections = [];

    //    public string $outName;

    //    public bool $outIgnore = false;

>>>>>>> 6af8e3a436675df3f65f507f4e1222a71c995b30
    //    public bool $existGetter = false;
    public array $outTranFromCollections = [];
    public string $propertyAliasName;

    /** @var DataGroupCollection|null */
    public ?DataGroupCollection $children;

<<<<<<< HEAD
    public  function __construct(ReflectionProperty $property, string $name,  mixed $type, ?PropertyKindEnum $kind)
=======
    public  function __construct(string $name,  mixed $defaultValue)
>>>>>>> 6af8e3a436675df3f65f507f4e1222a71c995b30
    {
        $this->property = $property;
        $this->name = $name;
        $this->defaultValue = $defaultValue;
    }

    public  function setChildren(?DataGroupCollection $collection): void
    {
        $this->children = &$collection;
    }
}
