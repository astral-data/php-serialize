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
    enum TestAnnotationEnum: string
    {
        case FIRST  = 'first';
        case SECOND = 'second';
    }

    $groups = new Groups(TestAnnotationEnum::FIRST, TestAnnotationEnum::SECOND);

    expect($groups->names)
        ->toBe(['FIRST', 'SECOND']);
});

it('creates groups with mixed types', function () {
    enum TestAnnotationEnumBack: string
    {
        case FIRST = 'first';
    }

    $groups = new Groups('group1', 123, TestAnnotationEnumBack::FIRST);

    expect($groups->names)
        ->toBe(['group1', '123', 'FIRST']);
});

it('creates groups with mixed enums and strings', function () {
    enum TestAnnotationEnumUnion: string
    {
        case ADMIN = 'admin';
        case USER  = 'user';
    }

    // 使用字符串和枚举混合创建 Groups 实例
    $groups = new Groups('group1', TestAnnotationEnumUnion::ADMIN, 'group2', TestAnnotationEnumUnion::USER);

    // 验证结果
    expect($groups->names)
        ->toBe(['group1', 'ADMIN', 'group2', 'USER']);
});

it('handles no names gracefully', function () {
    $groups = new Groups();

    expect($groups->names)
        ->toBe([]);
});
