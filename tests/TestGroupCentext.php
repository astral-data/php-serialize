<?php

use Astral\Serialize\Annotations\Groups;
use Astral\Serialize\Annotations\DataCollection\InputName;
use Astral\Serialize\Serialize;
use Astral\Serialize\Support\Context\SerializeContext;
use Astral\Serialize\Tests\TestRequest\Other\OtherTypeDoc;
use Astral\Serialize\Tests\TestRequest\TypeOneDoc;

beforeEach(function () {
    /** @var SerializeContext $this */
    //    $this->context = ContextFactory::build(TypeOneDoc::class, []);
});

it('test parse serialize class', function () {


    // 定义第一个关联数组
    $array1 = [
        'type_collect_object' => 'type_collect_object',
        'input_name'          => 'input_name'
    ];

    // 定义第二个索引数组
    $array2 = [
        'input_name',
        'type_string',
        'type_object',
        'type_int'
    ];

    #[Groups('test_1', 'test_2', 'test_3')]
    class TestGroupSerialize extends Serialize
    {
        public function __construct(
            #[Groups('test_1')]
            public readonly string $type_string,
            #[Groups('test', 'test_2')]
            public readonly object $type_object,
            #[Groups('test_2')]
            public readonly int $type_int,
            #[Groups('test_3')]
            public int $type_null,
            int $abc
            //            public readonly float $type_float,
            //            public readonly mixed $type_mixed_other,
            //            #[InputName('input_name')]
            //            public readonly array|object $type_collect_object,
        ) {
            $this->type_null = $abc;
        }
    }

    $startMemory = memory_get_usage();

    $instance = TestGroupSerialize::setGroups(['test_1']);

    //    print_r($instance->from());

    $res = $instance->from(
        type_string:'111',
        type_int:99999,
        type_object:new stdClass(),
        type_null:123,
        abc: 110
    );

    print_r($res);

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
