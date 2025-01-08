<?php

use Astral\Serialize\Exceptions\ValueCastError;
use Astral\Serialize\Annotations\DataCollection\OutName;
use Astral\Serialize\Annotations\DataCollection\InputName;
use Astral\Serialize\Serialize;
use Astral\Serialize\Annotations\DataCollection\OutIgnore;

beforeAll(function () {
    enum TestEnums
    {
        case NAME_ONE;
        case NAME_TWO;
    }
    class TestEnumFromSerialize extends Serialize
    {
        public TestEnums $enum;
        public string|TestEnums $enum_2;

    }
});

it('test enum serialize class', function () {
    $object  = TestEnumFromSerialize::from(enum:'NAME_ONE', abc:123);
    expect($object)->toBeInstanceOf(TestEnumFromSerialize::class)
        ->and($object->enum)->toBeInstanceOf(TestEnums::class)
        ->and($object->enum)->toBe(TestEnums::NAME_ONE);
});

it('test not find enum serialize class', function () {
    TestEnumFromSerialize::from(enum:'NAME_ONE-NO-FIND', abc:123);
})->throws(ValueCastError::class);

it('test enum union string serialize class', function () {
    $object1  = TestEnumFromSerialize::from(enum_2:'NAME_ONE-NO-FIND', abc:123);
    $object2  = TestEnumFromSerialize::from(enum_2:'NAME_TWO', abc:123);

    expect($object1)->toBeInstanceOf(TestEnumFromSerialize::class)
        ->and($object1->enum_2)->toBe('NAME_ONE-NO-FIND')
        ->and($object2->enum_2)->toBeInstanceOf(TestEnums::class)
        ->and($object2->enum_2)->toBe(TestEnums::NAME_TWO);

});
