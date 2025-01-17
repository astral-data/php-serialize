<?php

use Astral\Serialize\Support\Mappers\DotCaseMapper;
use Astral\Serialize\Support\Mappers\CamelCaseMapper;
use Astral\Serialize\Support\Mappers\SnakeCaseMapper;
use Astral\Serialize\Support\Mappers\KebabCaseMapper;
use Astral\Serialize\Support\Mappers\PascalCaseMapper;
use Astral\Serialize\Support\Mappers\ScreamingSnakeCaseMapper;

beforeAll(function () {
});

// 测试 CamelCase 转换
test('CamelCaseMapper converts to camelCase', function () {
    $mapper = new CamelCaseMapper();

    expect($mapper->resolve('first_name'))->toBe('firstName')
        ->and($mapper->resolve('first-name'))->toBe('firstName')
        ->and($mapper->resolve('FirstName'))->toBe('firstName')
        ->and($mapper->resolve('first.name'))->toBe('firstName')
        ->and($mapper->resolve('FIRST_NAME'))->toBe('firstName');
});

// 测试 SnakeCase 转换
test('SnakeCaseMapper converts to snake_case', function () {
    $mapper = new SnakeCaseMapper();

    expect($mapper->resolve('firstName'))->toBe('first_name')
        ->and($mapper->resolve('first-name'))->toBe('first_name')
        ->and($mapper->resolve('FirstName'))->toBe('first_name')
        ->and($mapper->resolve('first.name'))->toBe('first_name')
        ->and($mapper->resolve('FIRST_NAME'))->toBe('first_name');
});

// 测试 KebabCase 转换
test('KebabCaseMapper converts to kebab-case', function () {
    $mapper = new KebabCaseMapper();

    expect($mapper->resolve('firstName'))->toBe('first-name')
        ->and($mapper->resolve('first_name'))->toBe('first-name')
        ->and($mapper->resolve('FirstName'))->toBe('first-name')
        ->and($mapper->resolve('first.name'))->toBe('first-name')
        ->and($mapper->resolve('FIRST_NAME'))->toBe('first-name');
});

// 测试 PascalCase 转换
test('PascalCaseMapper converts to PascalCase', function () {
    $mapper = new PascalCaseMapper();

    expect($mapper->resolve('firstName'))->toBe('FirstName')
        ->and($mapper->resolve('first_name'))->toBe('FirstName')
        ->and($mapper->resolve('first-name'))->toBe('FirstName')
        ->and($mapper->resolve('first.name'))->toBe('FirstName')
        ->and($mapper->resolve('FIRST_NAME'))->toBe('FirstName');
});

// 测试 DotCase 转换
test('DotCaseMapper converts to dot.case', function () {
    $mapper = new DotCaseMapper();

    expect($mapper->resolve('firstName'))->toBe('first.name')
        ->and($mapper->resolve('first_name'))->toBe('first.name')
        ->and($mapper->resolve('first-name'))->toBe('first.name')
        ->and($mapper->resolve('FirstName'))->toBe('first.name')
        ->and($mapper->resolve('FIRST_NAME'))->toBe('first.name');
});

// 测试 ScreamingSnakeCase 转换
test('ScreamingSnakeCaseMapper converts to SCREAMING_SNAKE_CASE', function () {
    $mapper = new ScreamingSnakeCaseMapper();

    expect($mapper->resolve('firstName'))->toBe('FIRST_NAME')
        ->and($mapper->resolve('first_name'))->toBe('FIRST_NAME')
        ->and($mapper->resolve('first-name'))->toBe('FIRST_NAME')
        ->and($mapper->resolve('FirstName'))->toBe('FIRST_NAME')
        ->and($mapper->resolve('first.name'))->toBe('FIRST_NAME');
});
