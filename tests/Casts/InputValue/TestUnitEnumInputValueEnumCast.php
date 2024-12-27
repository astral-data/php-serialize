<?php

use Astral\Serialize\Casts\InputValue\InputValueEnumCast;
use Astral\Serialize\Enums\TypeKindEnum;
use Astral\Serialize\Exceptions\ValueCastError;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Collections\TypeCollection;
use Astral\Serialize\Support\Context\InputValueContext;

enum TestUnitEnum
{
    case OPTION_ONE;
    case OPTION_TWO;
}

enum TestUnitTryFromEnum
{
    case OPTION_ONE;
    case OPTION_TWO;

    public static function tryFrom(string $name): ?self
    {
        return match ($name) {
            'one'   => 'other',
            'two'   => self::OPTION_ONE,
            default => null,
        };
    }
}


beforeEach(function () {
    $this->cast       = new InputValueEnumCast();
    $this->collection = Mockery::mock(DataCollection::class);
    $this->context    = Mockery::mock(InputValueContext::class);
});


test('match returns true for valid unit enum value', function () {

    $typeCollection            = Mockery::mock(TypeCollection::class);
    $typeCollection->kind      = TypeKindEnum::ENUM;
    $typeCollection->className = TestUnitEnum::class;
    $this->collection->shouldReceive('getChooseType')->andReturn($typeCollection);

    $result = $this->cast->match('OPTION_ONE', $this->collection, $this->context);

    expect($result)->toBeTrue();
});

test('resolve returns correct unit enum instance for valid value', function () {

    $typeCollection            = Mockery::mock(TypeCollection::class);
    $typeCollection->kind      = TypeKindEnum::ENUM;
    $typeCollection->className = TestUnitEnum::class;
    $this->collection->shouldReceive('getChooseType')->andReturn($typeCollection);

    $result = $this->cast->resolve('OPTION_ONE', $this->collection, $this->context);

    expect($result)->toBe(TestUnitEnum::OPTION_ONE);
});

test('resolve throws ValueCastError for invalid unit enum value', function () {

    $typeCollection            = Mockery::mock(TypeCollection::class);
    $typeCollection->kind      = TypeKindEnum::ENUM;
    $typeCollection->className = TestUnitEnum::class;
    $this->collection->shouldReceive('getChooseType')->andReturn($typeCollection);

    $this->cast->resolve('INVALID_OPTION', $this->collection, $this->context);

})->throws(ValueCastError::class, 'Enum value "INVALID_OPTION" not found in classes: TestUnitEnum');



test('match returns true for valid unit try enum value', function () {

    $typeCollection            = Mockery::mock(TypeCollection::class);
    $typeCollection->kind      = TypeKindEnum::ENUM;
    $typeCollection->className = TestUnitTryFromEnum::class;
    $this->collection->shouldReceive('getChooseType')->andReturn($typeCollection);

    $result = $this->cast->match('other', $this->collection, $this->context);

    expect($result)->toBeTrue();
});
