<?php

use Astral\Serialize\Annotations\DataCollection\OutName;
use Astral\Serialize\Annotations\DataCollection\InputName;
use Astral\Serialize\Serialize;
use Astral\Serialize\Tests\TestTypeDoc\Other\OtherTypeDoc;
use Astral\Serialize\Annotations\DataCollection\OutIgnore;

it('test parse serialize class', function () {

    class TestSerialize extends Serialize
    {
        public function __construct(
            public readonly string $type_string,
            public readonly object $type_object,
            #[OutName('out_type_int')]
            #[OutName('out_type_int_2', TestSerialize::class)]
            public readonly int $type_int,
            #[OutName('out_type_null')]
            #[OutName('out_type_null_2')]
            public int $type_null,
            #[OutName('out_type_float', TestSerialize::class)]
            public readonly float $type_float,
            #[OutIgnore(TestSerialize::class)]
            public readonly mixed $type_mixed_other,
            #[InputName('input_name')]
            public readonly array|object $type_collect_object,
            int $abc,
        ) {
            $this->type_null = $abc;
        }
    }

    // 记录测试开始前的内存使用
    $startMemory = memory_get_usage();

    $object  = TestSerialize::from(
        ['input_name' => [new OtherTypeDoc()],'type_string' => 'test_string','type_object' => new StdClass(),'type_int' => 11,'type_float' => 0.02],
        type_float:null,
        input_name:null,
        type_object:null,
        type_mixed_other: ['abc' => ['bbb' => ['ccc' => 'dddd'],['abc']],'aaa','bbb','ccc',''],
        abc:123
    );

    print_r($object->toArray());


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
