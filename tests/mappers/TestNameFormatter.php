<?php

use Astral\Serialize\Support\Mappers\DotCaseMapper;
use Astral\Serialize\Support\Mappers\CamelCaseMapper;
use Astral\Serialize\Support\Mappers\SnakeCaseMapper;
use Astral\Serialize\Support\Mappers\KebabCaseMapper;
use Astral\Serialize\Support\Mappers\PascalCaseMapper;
use Astral\Serialize\Support\Mappers\ScreamingSnakeCaseMapper;

beforeAll(function () {
});

test('CamelCaseMapper converts to camelCase', function () {
    $mapper = new CamelCaseMapper();

    expect($mapper->resolve('first_name'))->toBe('firstName')
        ->and($mapper->resolve('first-name'))->toBe('firstName')
        ->and($mapper->resolve('FirstName'))->toBe('firstName')
        ->and($mapper->resolve('first.name'))->toBe('firstName')
        ->and($mapper->resolve('FIRST_NAME'))->toBe('firstName');
});

test('SnakeCaseMapper converts to snake_case', function () {
    $mapper = new SnakeCaseMapper();

    expect($mapper->resolve('firstName'))->toBe('first_name')
        ->and($mapper->resolve('first-name'))->toBe('first_name')
        ->and($mapper->resolve('FirstName'))->toBe('first_name')
        ->and($mapper->resolve('first.name'))->toBe('first_name')
        ->and($mapper->resolve('FIRST_NAME'))->toBe('first_name');
});

test('KebabCaseMapper converts to kebab-case', function () {
    $mapper = new KebabCaseMapper();

    expect($mapper->resolve('firstName'))->toBe('first-name')
        ->and($mapper->resolve('first_name'))->toBe('first-name')
        ->and($mapper->resolve('FirstName'))->toBe('first-name')
        ->and($mapper->resolve('first.name'))->toBe('first-name')
        ->and($mapper->resolve('FIRST_NAME'))->toBe('first-name');
});

test('PascalCaseMapper converts to PascalCase', function () {
    $mapper = new PascalCaseMapper();

    expect($mapper->resolve('firstName'))->toBe('FirstName')
        ->and($mapper->resolve('first_name'))->toBe('FirstName')
        ->and($mapper->resolve('first-name'))->toBe('FirstName')
        ->and($mapper->resolve('first.name'))->toBe('FirstName')
        ->and($mapper->resolve('FIRST_NAME'))->toBe('FirstName');
});

test('DotCaseMapper converts to dot.case', function () {
    $mapper = new DotCaseMapper();

    expect($mapper->resolve('firstName'))->toBe('first.name')
        ->and($mapper->resolve('first_name'))->toBe('first.name')
        ->and($mapper->resolve('first-name'))->toBe('first.name')
        ->and($mapper->resolve('FirstName'))->toBe('first.name')
        ->and($mapper->resolve('FIRST_NAME'))->toBe('first.name');
});

test('ScreamingSnakeCaseMapper converts to SCREAMING_SNAKE_CASE', function () {
    $mapper = new ScreamingSnakeCaseMapper();

    expect($mapper->resolve('firstName'))->toBe('FIRST_NAME')
        ->and($mapper->resolve('first_name'))->toBe('FIRST_NAME')
        ->and($mapper->resolve('first-name'))->toBe('FIRST_NAME')
        ->and($mapper->resolve('FirstName'))->toBe('FIRST_NAME')
        ->and($mapper->resolve('first.name'))->toBe('FIRST_NAME');
});
