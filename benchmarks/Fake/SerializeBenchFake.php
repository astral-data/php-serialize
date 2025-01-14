<?php

namespace Astral\Benchmarks\Fake;

use Astral\Serialize\Annotations\InputValue\InputDateFormat;
use Astral\Serialize\Serialize;
use DateTime;

class SerializeBenchFake extends Serialize
{
    public $withoutType;

    public int $int;

    public bool $bool;
    public float $float;

    public string $string;

    public array $array;
    public ?int $nullable;

    public mixed $mixed;
    public DateTime $defaultDateTime;

    #[InputDateFormat('Y-m-d H:i:s')]
    public string|DateTime $stringDateTime;

    /** @var NestedCollectionFake[] */
    public ?array $nestedCollection;

    public array $nestedArray;


}
