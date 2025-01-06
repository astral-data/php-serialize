<?php

use Astral\Serialize\Annotations\Groups;
use Astral\Serialize\Serialize;
use Astral\Serialize\Support\Context\SerializeContext;

beforeEach(function () {
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

it('test parse groups serialize class', function () {

    $startMemory = memory_get_usage();

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

    $otherObject = new OtherObject();
    $otherObject->id = 123;
    $otherObject->name = 'abc';
    $instance = TestGroupSerialize::setGroups(['test_2'])->from(
        type_string:'111',
        type_int:99999,
        type_object:$otherObject,
        type_null:123,
        type_float:998,
        abc: 110
    );

    expect($instance)->toBeInstanceOf(TestGroupSerialize::class)
        ->and($instance->type_string)->toBeString('')
        ->and($instance->type_int)->toBeInt(99999)
        ->and($instance->type_null)->toBeInt(110)
        ->and($instance->type_float)->toBeFloat(0)
        ->and($instance->type_mixed_other)->toBeNull()
        ->and($instance->type_object)->toBeInstanceOf(OtherObject::class)
        ->and($instance->type_object->name)->toBeString('abc')
        ->and($instance->type_object->id)->toBeInt(123)
    ;


    // 记录测试结束后的内存使用
    $endMemory = memory_get_usage();

    // 记录峰值内存
    $peakMemory = memory_get_peak_usage();

    // 计算使用内存
    $memoryUsed = $endMemory - $startMemory;

    // 输出内存使用情况
    echo sprintf(
        "Start Memory: %.2f MB\nEnd Memory: %.2f MB\nMemory Used: %.2f MB\nPeak Memory: %.2f MB\n",
        $startMemory / 1024 / 1024,
        $endMemory   / 1024 / 1024,
        $memoryUsed  / 1024 / 1024,
        $peakMemory  / 1024 / 1024
    );
});
