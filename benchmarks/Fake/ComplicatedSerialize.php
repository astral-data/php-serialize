<?php

use Astral\Serialize\Serialize;

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
