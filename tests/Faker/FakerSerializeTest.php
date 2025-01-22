<?php

use Astral\Serialize\Annotations\Faker\FakerCollection;
use Astral\Serialize\Annotations\Faker\FakerMethod;
use Astral\Serialize\Annotations\Faker\FakerObject;
use Astral\Serialize\Annotations\Faker\FakerValue;
use Astral\Serialize\Serialize;

beforeAll(function () {

    class TestService
    {
        public function testMethod(TestNestedDataFake $request): string
        {
            return $request->string . 'abc';
        }
    }

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


        #[FakerMethod(TestService::class, 'testMethod')]
        public string $testAction;

        public $withoutType;
        public int $int;

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

        #[FakerObject(['id','value'])]
        public array $object;
    }

});

it('test  faker Serialize class', function () {

    $res = TestFakerSerialize::faker();

    expect($res)->toBeInstanceOf(TestFakerSerialize::class)
        ->and($res->name)->toBeString()
        ->and($res->username)->toBeString()
        ->and($res->testAction)->toContain('abc')
        ->and($res->int)->toBeInt()
        ->and($res->bool)->toBeBool()
        ->and($res->float)->toBeFloat()
        ->and($res->string)->toBeString()
        ->and($res->array)->toBeArray()
        ->and($res->array)->toHaveCount(3);

    foreach ($res->array as $arrayItem) {

        expect($arrayItem)->toBeArray()
            ->and($arrayItem)->toHaveKey('name')
            ->and($arrayItem['name'])->toBeString()
            ->and($arrayItem)->toHaveKey('username')
            ->and($arrayItem['username'])->toBeString()
            ->and($arrayItem)->toHaveKey('array')
            ->and($arrayItem['array'])->toBeArray();

        foreach ($arrayItem['array'] as $subItem) {
            expect($subItem)->toHaveKey('id')
                ->and($subItem['id'])->toBeString()
                ->and($subItem)->toHaveKey('value')
                ->and($subItem['value'])->toBeString();
        }
    }

    expect(in_array(gettype($res->nullable), ['integer', 'NULL']))->toBeTrue()
        ->and($res->mixed)->not()->toBeNull()
        ->and($res->enum)->toBeInstanceOf(TestFakerEnum::class)
        ->and(in_array($res->enum, TestFakerEnum::cases()))->toBeTrue()
        ->and($res->defaultDateTime)->toBeInstanceOf(DateTime::class)
        ->and($res->stringDate)->toBeString()
        ->and($res->nestedCollection)->toBeArray()
        ->and($res->nestedCollection)->toHaveCount(2);

    foreach ($res->nestedCollection as $nested) {

        expect($nested)->toBeInstanceOf(TestNestedCollectionFake::class)
            ->and($nested->int)->toBeInt()
            ->and($nested->string)->toBeString()
            ->and($nested->nestedData)->toBeArray()
            ->and($nested->nestedData)->toHaveCount(3);

        foreach ($nested->nestedData as $nestedData) {
            expect($nestedData)->toBeInstanceOf(TestNestedDataFake::class)
                ->and($nestedData->string)->toBeString();
        }
    }

    expect($res->objectCollection)->toBeInstanceOf(TestNestedCollectionFake::class)
        ->and($res->objectCollection->int)->toBeInt()
        ->and($res->objectCollection->string)->toBeString()
        ->and($res->objectCollection->nestedData)->toBeArray();

    foreach ($res->objectCollection->nestedData as $nestedData) {
        expect($nestedData)->toBeInstanceOf(TestNestedDataFake::class)
            ->and($nestedData->string)->toBeString();
    }

    expect($res->object)->toBeArray()
        ->and($res->object)->toHaveKey('id')
        ->and($res->object['id'])->toBeString()
        ->and($res->object)->toHaveKey('value')
        ->and($res->object['value'])->toBeString();
});
