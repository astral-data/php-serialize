<?php

use Astral\Serialize\Exceptions\ValueCastError;
use Astral\Serialize\Annotations\DataCollection\OutName;
use Astral\Serialize\Annotations\DataCollection\InputName;
use Astral\Serialize\Serialize;
use Astral\Serialize\Annotations\DataCollection\OutIgnore;

beforeAll(function () {
    class TestFromSerialize extends Serialize
    {
        public $withoutType;

        public function __construct(
            public readonly string $type_string,
            public readonly object $type_object,
            #[OutName('out_type_int')]
            #[OutName('out_type_int_2', TestFromSerialize::class)]
            public readonly int $type_int,
            #[OutName('out_type_null')]
            #[OutName('out_type_null_2')]
            public int $type_null,
            #[OutName('out_type_float', TestFromSerialize::class)]
            public readonly float $type_float,
            #[OutIgnore(TestFromSerialize::class)]
            public readonly mixed $type_mixed_other,
            #[InputName('input_name')]
            public readonly array|object $type_collect_object,
            int $abc,
        ) {
            $this->type_null = $abc;
        }
    }
});

it('test parse serialize class', function () {

    $object  = TestFromSerialize::from(
        [
            'input_name' => [ fn () => new stdClass()],
            'type_string' => 'test_string',
            'type_object' => new StdClass(),
            'type_int' => 11,
            'type_float' => 0.02,
            'withoutType' => 'hhh',
        ],
        type_float:null,
        input_name:null,
        type_object:null,
        type_mixed_other: ['abc' => ['bbb' => ['ccc' => 'dddd'],['abc']],'aaa','bbb','ccc',''],
        abc:123
    );

    print_r($object->toArray());
});
