<?php

use Astral\Serialize\Annotations\Groups;
use Astral\Serialize\Annotations\DataCollection\InputIgnore;
use Astral\Serialize\Serialize;

beforeAll(function () {

    class InputIgnoreSerialize extends Serialize
    {
        #[InputIgnore('admin')]
        #[Groups('admin')]
        public string $name;

        #[Groups('admin')]
        public string $secretKey;

        #[InputIgnore]
        #[Groups('admin')]
        public string $sensitiveInfo;
    }
});

it('tests InputIgnore Serialize class', function () {

    $object = InputIgnoreSerialize::from([
        'name' => '张三',
        'secretKey' => 'confidential',
        'sensitiveInfo' => '机密信息',
    ]);

    expect($object)->toBeInstanceOf(InputIgnoreSerialize::class)
        ->and($object->name)->toBe('张三')
        ->and($object->secretKey)->toBe('confidential')
        ->and($object->sensitiveInfo ?? null)->toBeNull();

    $array = $object->toArray();
    expect($array)->toMatchArray([
        'name' => '张三',
        'secretKey' => 'confidential',
        'sensitiveInfo' => null,
    ]);

    $object2 = InputIgnoreSerialize::setGroups('admin')->from([
        'name' => '张三',
        'secretKey' => 'confidential',
        'sensitiveInfo' => '机密信息',
    ]);

    expect($object2->name ?? null)->toBeNull()
        ->and($object2->secretKey)->toBe('confidential')
        ->and($object2->sensitiveInfo ?? null)->toBeNull(); // 分组 'admin' 应忽略 name

    $array2 = $object2->toArray();
    expect($array2)->toMatchArray([
        'name' => null,
        'secretKey' => 'confidential',
        'sensitiveInfo' => null,
    ]);
});
