<?php

use Astral\Serialize\Context;
use Astral\Serialize\Support\Factories\ContextFactory;
use Astral\Serialize\Tests\TestRequest\Other\OtherTypeDoc;
use Astral\Serialize\Tests\TestRequest\TypeOneDoc;

beforeEach(function () {
    /** @var Context $this */
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

    // 输出调试信息，检查两个数组内容
    //    var_dump(array_values($array1));
    //    var_dump($array2);

    // 比较两个数组的值
    $result = array_intersect(array_values($array1), $array2);

    // 输出交集结果
    //    print_r($result);

    // 记录测试开始前的内存使用
    $startMemory = memory_get_usage();

    //    $result =  $this->context->parseSerializeClass(Context::DEFAULT_GROUP_NAME, TypeOneDoc::class);

    $object  = TypeOneDoc::from(
        ['input_name' => [new OtherTypeDoc()],'type_string' => 'test_string','type_object' => new StdClass(),'type_int' => 11],
        type_float:0.01
    );
    print_r($object);


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
