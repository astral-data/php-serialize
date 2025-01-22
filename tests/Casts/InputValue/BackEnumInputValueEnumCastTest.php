<?php

use Astral\Serialize\Casts\InputValue\InputValueEnumCast;
use Astral\Serialize\Enums\TypeKindEnum;
use Astral\Serialize\Exceptions\ValueCastError;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Collections\TypeCollection;
use Astral\Serialize\Support\Context\InputValueContext;

beforeAll(function () {
    enum TestEnum: string
    {
        case OPTION_ONE = 'one';
        case OPTION_TWO = 'two';
    }
});

beforeEach(function () {
    $this->cast       = new InputValueEnumCast();
    $this->collection = Mockery::mock(DataCollection::class);
    $this->context    = Mockery::mock(InputValueContext::class);
});

test('match returns true for valid enum value', function () {

    $typeCollection            = Mockery::mock(TypeCollection::class);
    $typeCollection->kind      = TypeKindEnum::ENUM;
    $typeCollection->className = TestEnum::class;
    $this->collection->shouldReceive('getTypes')->andReturn([$typeCollection]);

    $result = $this->cast->match('one', $this->collection, $this->context);

    expect($result)->toBeTrue();
});

test('match returns false for invalid enum kind', function () {

    $typeCollection            = Mockery::mock(TypeCollection::class);
    $typeCollection->kind      = TypeKindEnum::MIXED;
    $typeCollection->className = TestEnum::class;

    $this->collection->shouldReceive('getTypes')->andReturn([$typeCollection]);
    $result = $this->cast->match('one', $this->collection, $this->context);

    expect($result)->toBeFalse();
});

test('resolve returns correct enum instance for valid value', function () {

    $typeCollection            = Mockery::mock(TypeCollection::class);
    $typeCollection->kind      = TypeKindEnum::ENUM;
    $typeCollection->className = TestEnum::class;
    $this->collection->shouldReceive('getTypes')->andReturn([$typeCollection]);

    $result = $this->cast->resolve('one', $this->collection, $this->context);

    expect($result)->toBe(TestEnum::OPTION_ONE);
});

test('resolve throws ValueCastError for invalid enum value', function () {

    $typeCollection            = Mockery::mock(TypeCollection::class);
    $typeCollection->kind      = TypeKindEnum::ENUM;
    $typeCollection->className = TestEnum::class;
    $this->collection->shouldReceive('getTypes')->andReturn([$typeCollection]);

    $this->cast->resolve('invalid_value', $this->collection, $this->context);

})->throws(ValueCastError::class, 'Enum value "invalid_value" not found in EnumClass: TestEnum');
