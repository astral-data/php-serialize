<?php

use Astral\Serialize\Serialize;
use Astral\Serialize\Annotations\DataCollection\OutName;
use Astral\Serialize\Annotations\DataCollection\OutIgnore;
use Astral\Serialize\Annotations\DataCollection\InputName;

class ComplicatedSerialize extends Serialize
{
    public $withoutType;

    public int $int;

    public bool $bool;
    public float $float;

    public string $string;

    public array $array;
    public ?int $nullable;

    public mixed $mixed;
    public $explicitCast;
    public DateTime $defaultCast;
    public ?DataCollection $nestedCollection;

    public array $nestedArray;


}
