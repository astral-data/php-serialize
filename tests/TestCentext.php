<?php

use Astral\Serialize\Serialize;
use Astral\Serialize\Support\Context\SerializeContext;
use Astral\Serialize\Tests\TestRequest\Other\OtherTypeDoc;
use Astral\Serialize\Tests\TestRequest\TypeOneDoc;
use Astral\Serialize\Annotations\DataCollection\InputName;

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

    class TestSerialize extends Serialize
    {
        public function __construct(
            public readonly string $type_string,
            public readonly object $type_object,
            public readonly int $type_int,
            //            public readonly float $type_float,
            //            public readonly mixed $type_mixed_other,
            //            #[InputName('input_name')]
            //            public readonly array|object $type_collect_object,
        ) {

        }
    }

//        $reflection = new ReflectionClass(TestSerialize::class);
//        $constructor = $reflection->getConstructor();
//        $instance = $reflection->newInstanceWithoutConstructor(); // 跳过自动调用构造函数
//        $reflection->getProperty('type_int')->setValue($instance,'1234');
//    //    $constructor->invokeArgs($instance, ['type_string' => 111,'type_object' => new stdClass()]); // 手动调用构造函数
//
//    $reflection->getProperty('type_int')->setAccessible($accessible);
//        $instance->__construct('111', new stdClass(),11);
//        var_dump($instance);


    // 输出调试信息，检查两个数组内容
    //    var_dump(array_values($array1));
    //    var_dump($array2);

    // 比较两个数组的值
    //    $result = array_intersect(array_values($array1), $array2);

    // 输出交集结果
    //    print_r($result);

    // 记录测试开始前的内存使用
        $startMemory = memory_get_usage();
    //
    //    //    $result =  $this->context->parseSerializeClass(SerializeContext::DEFAULT_GROUP_NAME, TypeOneDoc::class);
    //
    $object  = TestSerialize::from(
        ['input_name' => [new OtherTypeDoc()],'type_string' => 'test_string','type_object' => new StdClass(),'type_int' => 11,'type_float' => 0.02],
        type_float:null,
        input_name:null,
        type_object:null,
        type_mixed_other: ['abc' => ['bbb' => ['ccc' => 'dddd'],['abc']],'aaa','bbb','ccc',''],
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
