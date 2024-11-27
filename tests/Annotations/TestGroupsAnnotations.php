<?php

use Astral\Serialize\Annotations\Groups;

it('creates groups with string names', function () {
    $groups = new Groups('group1', 'group2', 'group3');

    expect($groups->names)
        ->toBe(['group1', 'group2', 'group3']);
});

it('creates groups with integer names', function () {
    $groups = new Groups(1, 2, 3);

    expect($groups->names)
        ->toBe(['1', '2', '3']);
});

it('creates groups with UnitEnum values', function () {
    enum TestEnum: string
    {
        case FIRST = 'first';
        case SECOND = 'second';
    }

    $groups = new Groups(TestEnum::FIRST, TestEnum::SECOND);

    expect($groups->names)
        ->toBe(['FIRST', 'SECOND']);
});

it('creates groups with mixed types', function () {
    enum TestEnumBack: string
    {
        case FIRST = 'first';
    }

    $groups = new Groups('group1', 123, TestEnumBack::FIRST);

    expect($groups->names)
        ->toBe(['group1', '123', 'FIRST']);
});

it('creates groups with mixed enums and strings', function () {
    enum TestEnumUnion: string
    {
        case ADMIN = 'admin';
        case USER = 'user';
    }

    // 使用字符串和枚举混合创建 Groups 实例
    $groups = new Groups('group1', TestEnumUnion::ADMIN, 'group2', TestEnumUnion::USER);

    // 验证结果
    expect($groups->names)
        ->toBe(['group1', 'ADMIN', 'group2', 'USER']);
});

it('handles no names gracefully', function () {
    $groups = new Groups();

    expect($groups->names)
        ->toBe([]);
});
