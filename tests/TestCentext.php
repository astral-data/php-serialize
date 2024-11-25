<?php

use Astral\Serialize\Context;
use Astral\Serialize\Support\Factories\ContextFactory;
use Astral\Serialize\Tests\TestRequest\TypeOneDoc;

beforeEach(function () {
    /** @var Context $this */
    $this->context = ContextFactory::build(TypeOneDoc::class, []);
});

it('test parse serialize class', function () {

    // 记录测试开始前的内存使用
    $startMemory = memory_get_usage();

    $result =  $this->context->parseSerializeClass(Context::DEFAULT_GROUP_NAME, TypeOneDoc::class);

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
