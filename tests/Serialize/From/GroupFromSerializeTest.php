<?php

use Astral\Serialize\Annotations\Groups;
use Astral\Serialize\Serialize;

beforeAll(function () {
    class TestGroupSerialize extends Serialize
    {
        #[Groups('test_1')]
        public string $not_construct_string;

        #[Groups('test_2')]
        public int $not_construct_int;

        public function __construct(
            #[Groups('test_1')]
            public readonly string $type_string,
            #[Groups('test_1')]
            public readonly string $type_string_ignore,
            #[Groups('test', 'test_2')]
            public readonly object $type_object,
            #[Groups('test_2')]
            public readonly int    $type_int,
            #[Groups('test_3')]
            public int             $type_null,
            public readonly float  $type_float,
            public readonly mixed  $type_mixed_other,
            int                    $abc,
        ) {
            $this->type_null = $abc;
        }
    }

    class OtherObject
    {
        public string $name;
        public int $id;
    }
});

it('test parse groups Serialize class', function () {

    /** @var TestGroupSerialize  $instance */
    $instance = TestGroupSerialize::setGroups(['test_1'])->from(
        not_construct_string:'not_construct_string',
        not_construct_int:'001',
        type_string:'111',
        type_int:99999,
        type_object:new OtherObject(),
        type_null:123,
        type_float:998,
        abc: 110
    );

    expect($instance)->toBeInstanceOf(TestGroupSerialize::class)
        ->and($instance->type_string)->toBeString('111')
        ->and($instance->type_int)->toBeInt(0)
        ->and($instance->type_null)->toBeInt(110)
        ->and($instance->type_float)->toBeFloat(0)
        ->and($instance->type_mixed_other)->toBeNull()
        ->and($instance->not_construct_string)->toBeString('not_construct_string')
        ->and(isset($instance->not_construct_int))->toBeFalse()
        ->and($instance->type_object)->toBeInstanceOf(stdClass::class)
    ;


    $otherObject       = new OtherObject();
    $otherObject->id   = 123;
    $otherObject->name = 'abc';

    /** @var TestGroupSerialize  $instance2 */
    $instance2          = TestGroupSerialize::setGroups(['test_2'])->from(
        type_string:'111',
        type_int:99999,
        type_object:$otherObject,
        type_null:123,
        type_float:998,
        abc: 111
    );

    expect($instance2)->toBeInstanceOf(TestGroupSerialize::class)
        ->and($instance2->type_string)->toBeString('')
        ->and($instance2->type_int)->toBeInt(99999)
        ->and($instance2->type_null)->toBeInt(110)
        ->and($instance2->type_float)->toBeFloat(0)
        ->and($instance2->type_mixed_other)->toBeNull()
        ->and($instance2->type_object)->toBeInstanceOf(OtherObject::class)
        ->and($instance2->type_object->name)->toBeString('abc')
        ->and($instance2->type_object->id)->toBeInt(123)
    ;

});
