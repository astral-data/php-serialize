<?php

use Astral\Serialize\Annotations\DataCollection\InputIgnore;
use Astral\Serialize\Serialize;

beforeAll(function () {

    class OtherNestedOne
    {
        public string $name_one;
        public int $id_one;

        public OtherNestedTwo $otherNestedTwo;
    }

    class OtherNestedTwo
    {
        public string $name_two;
        public int $id_two;
        public OtherNestedThree $otherNestedThree;
    }

    class OtherNestedThree
    {
        public string $name_three;
        #[InputIgnore(TestNestedSerialize::class)]
        public int $id_three;
    }

    class TestNestedSerialize extends Serialize
    {
        public string $name;

        public int $id;

        public OtherNestedOne $otherNestedOne;

    }

});

it('test parse nested serialize class', function () {

    $startMemory = memory_get_usage();


    $instance = TestNestedSerialize::from(
        [
            'name'           => 'TestNestedSerialize-name',
            'id'             => 001,
            'otherNestedOne' => [
                'name_one'       => 'OtherNestedOne-name_one',
                'id_one'         => 002,
                'otherNestedTwo' => [
                    'name_two'         => 'OtherNestedTwo-name_two',
                    'id_two'           => 003,
                    'otherNestedThree' => [
                        'name_three' => 'OtherNestedThree-name_three',
                        'id_three'   => 004
                    ]
                ]
            ]
        ]
    );

    print_r($instance);

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
