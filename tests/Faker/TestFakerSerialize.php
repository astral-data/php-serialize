<?php

use Astral\Benchmarks\Fake\NestedDataFake;
use Astral\Benchmarks\Fake\NestedCollectionFake;
use Astral\Serialize\Annotations\Faker\FakerObject;
use Astral\Serialize\Annotations\Faker\FakerCollection;
use Astral\Serialize\Annotations\Faker\FakerValue;
use Astral\Serialize\Serialize;

beforeAll(function () {


    enum TestFakerEnum
    {
        case ONE;
        case ONE_TIME;
    }

    class TestNestedCollectionFake
    {
        public function __construct(
            public readonly int $int,
            public readonly string $string,
            /** @var TestNestedDataFake[] */
            #[FakerCollection(TestNestedDataFake::class, 3)]
            public readonly array $nestedData,
        ) {

        }
    }

    class TestNestedDataFake
    {
        public function __construct(
            public readonly string $string,
        ) {

        }
    }

    class TestFakerSerialize extends Serialize
    {
        #[FakerValue('name')]
        public string $name;
        #[FakerValue('uuid')]
        public string $username;

        public $withoutType;
        public int $int;

        #[FakerValue('boolean', ['chanceOfGettingTrue' => 50])]
        public bool $bool;
        public float $float;
        public string $string;

        #[FakerCollection(['name', 'username','array' => ['id','value']], num: 3)]
        public array $array;
        public ?int $nullable;
        public mixed $mixed;
        public TestFakerEnum $enum;

        public DateTime $defaultDateTime;
        public string $stringDate;

        /** @var TestNestedCollectionFake[] */
        #[FakerCollection(TestNestedCollectionFake::class, 2)]
        public array $nestedCollection;

        #[FakerObject(TestNestedCollectionFake::class)]
        public object $objectCollection;
    }

});

it('test  faker serialize class', function () {

    $res = TestFakerSerialize::faker();

    print_r($res);
});
