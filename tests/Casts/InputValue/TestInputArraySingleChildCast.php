<?php

use Astral\Serialize\Casts\InputValue\InputArraySingleChildCast;
use Astral\Serialize\Enums\TypeKindEnum;
use Astral\Serialize\Resolvers\InputResolver;
use Astral\Serialize\SerializeContainer;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Collections\GroupDataCollection;
use Astral\Serialize\Support\Context\InputValueContext;

beforeAll(function () {
    $this->cast       = new InputArraySingleChildCast();
    $this->collection = Mockery::mock(DataCollection::class);
    $this->context    = Mockery::mock(InputValueContext::class);
});

test('match returns true for valid array and single child collection', function () {
    $this->collection->shouldReceive('getChildren')->andReturn([
        Mockery::mock(GroupDataCollection::class)
    ]);

    $this->collection->shouldReceive('getTypes')->andReturn([
        (object) ['kind' => TypeKindEnum::COLLECT_SINGLE_OBJECT]
    ]);

    $result = $this->cast->match(['value1', 'value2'], $this->collection, $this->context);
    expect($result)->toBeTrue();
});

test('resolve correctly maps values for collect object type', function () {
    $child     = Mockery::mock(GroupDataCollection::class);
    $childType = (object) ['kind' => TypeKindEnum::COLLECT_SINGLE_OBJECT];

    $this->collection->shouldReceive('getChildren')->andReturn([$child]);
    $this->collection->shouldReceive('getTypes')->andReturn([$childType]);
    $this->collection->shouldReceive('setChooseType')->with($childType);

    $child->shouldReceive('getClassName')->andReturn('SomeClass');

    $values = ['value1', 'value2'];

    $resolverMock = Mockery::mock(InputResolver::class);
    $this->serializeContainerMock->shouldReceive('propertyInputValueResolver')->andReturn($resolverMock);

    $resolverMock->shouldReceive('resolve')->with('SomeClass', $child, 'value1')->andReturn('resolved1');
    $resolverMock->shouldReceive('resolve')->with('SomeClass', $child, 'value2')->andReturn('resolved2');

    $result = $this->cast->resolve($values, $this->collection, $this->context);
    expect($result)->toBe(['resolved1', 'resolved2']);
});


test('resolve returns single resolved value for non-collect object type', function () {
    $child     = Mockery::mock(GroupDataCollection::class);
    $childType = (object) ['kind' => TypeKindEnum::COLLECT_SINGLE_OBJECT];

    $this->collection->shouldReceive('getChildren')->andReturn([$child]);
    $this->collection->shouldReceive('getTypes')->andReturn([$childType]);
    $this->collection->shouldReceive('setChooseType')->with($childType);

    $child->shouldReceive('getClassName')->andReturn('SomeClass');

    $resolverMock = Mockery::mock(InputResolver::class);
    SerializeContainer::get()->shouldReceive('propertyInputValueResolver')->andReturn($resolverMock);

    $resolverMock->shouldReceive('resolve')->with('SomeClass', $child, 'value')->andReturn('resolved_value');

    $result = $this->cast->resolve('value', $this->collection, $this->context);
    expect($result)->toBe('resolved_value');
});
