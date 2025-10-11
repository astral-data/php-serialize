<?php

use Astral\Serialize\Annotations\DataCollection\InputName;
use Astral\Serialize\Annotations\DataCollection\OutputIgnore;
use Astral\Serialize\Annotations\DataCollection\OutputName;
use Astral\Serialize\Serialize;

beforeAll(function () {
    class TestFromSerialize extends Serialize
    {
        public $withoutType;

        public function __construct(
            public readonly string $type_string,
            public readonly object $type_object,
            #[OutputName('out_type_int')]
            #[OutputName('out_type_int_2', TestFromSerialize::class)]
            public readonly int $type_int,
            #[OutputName('out_type_null')]
            #[OutputName('out_type_null_2')]
            public int $type_null,
            #[OutputName('out_type_float', TestFromSerialize::class)]
            public readonly float $type_float,
            #[OutputIgnore(TestFromSerialize::class)]
            public readonly mixed $type_mixed_other,
            #[InputName('input_name')]
            public readonly array|object $type_collect_object,
            int $abc,
        ) {
            $this->type_null = $abc;
        }
    }

    class TestConstructFromSerialize extends Serialize
    {
        public function __construct(
            public string $type_string,
            public object $type_object,
            public int $type_int,
            public int $type_null,
            public float $type_float,
        ) {
        }
    }

    class TestConstructValidationFromSerialize extends Serialize
    {

        public string $type_string;

        public function validate():void
        {
            if($this->type_string !== '123'){
                throw new Exception('type_string is not 123');
            }

            $this->type_string = '234';

        }
    }
});

it('test parse Serialize class', function () {

    $object  = TestFromSerialize::from(
        [
            'input_name'  => [ fn () => new stdClass()],
            'type_string' => 'test_string',
            'type_object' => new StdClass(),
            'type_int'    => 11,
            'type_float'  => 0.02,
            'withoutType' => 'hhh',
        ],
        type_float:null, //  'type_float'  => 0.02 change to null
        input_name:null,
        type_object:null,
        type_mixed_other: ['abc' => ['bbb' => ['ccc' => 'dddd'],['abc']],'aaa','bbb','ccc',''],
        abc:123
    );

    expect($object)->toBeInstanceOf(TestFromSerialize::class)
        ->and($object->withoutType)->toBe('hhh')
        ->and($object->type_string)->toBe('test_string')
        ->and($object->type_object)->toBeInstanceOf(StdClass::class)
        ->and($object->type_int)->toBe(11)
        ->and($object->type_null)->toBe(123)
        ->and($object->type_float)->toBe(0.0)
        ->and($object->type_mixed_other)->toBeArray()
        ->and($object->type_mixed_other['abc']['bbb']['ccc'])->toBe('dddd')
        ->and($object->type_collect_object)->toBeInstanceOf(StdClass::class);
});

it('test parse construct Serialize class', function () {

    $object = TestConstructFromSerialize::from(
        type_string: 123,
        type_object: null,
        type_int: '123',
        type_null: null,
        type_float: '0.01',
    );

    expect($object)->toBeInstanceOf(TestConstructFromSerialize::class)
        ->and($object->type_string)->toBe('123')
        ->and($object->type_object)->toBeInstanceOf(StdClass::class)
        ->and($object->type_int)->toBe(123)
        ->and($object->type_null)->toBe(0)
        ->and($object->type_float)->toBe(0.01);
});

it('throws exception when type_string is not 123', function () {
    expect(function () {
        TestConstructValidationFromSerialize::from(type_string: 111);
    })->toThrow(Exception::class, 'type_string is not 123');
});

it('creates object successfully when type_string is 123', function () {
    $object = TestConstructValidationFromSerialize::from(type_string: 123);

    expect($object)
        ->toBeInstanceOf(TestConstructValidationFromSerialize::class)
        ->and($object->type_string)->toBe('234');
});