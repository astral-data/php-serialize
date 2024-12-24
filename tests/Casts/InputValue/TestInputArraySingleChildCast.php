<?php

use Astral\Serialize\Resolvers\PropertyInputValueResolver;
use Astral\Serialize\Casts\InputValue\InputArraySingleChildCast;
use Astral\Serialize\Enums\TypeKindEnum;
use Astral\Serialize\Exceptions\NotFoundAttributePropertyResolver;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Collections\DataGroupCollection;
use Astral\Serialize\Support\Context\InputValueContext;
use Astral\Serialize\SerializeContainer;
use Mockery;

// TODO

beforeEach(function () {
    $this->cast = new InputArraySingleChildCast();
    $this->collection = Mockery::mock(DataCollection::class);
    $this->context = Mockery::mock(InputValueContext::class);

    // 替换 SerializeContainer 为 Mock
//    $this->serializeContainerMock = Mockery::mock(SerializeContainer::class)->makePartial();
//    SerializeContainer::getInstanceForTest($this->serializeContainerMock);
});

test('match returns true for valid array and single child collection', function () {
    $this->collection->shouldReceive('getChildren')->andReturn([
        Mockery::mock(DataGroupCollection::class)
    ]);

    $this->collection->shouldReceive('getTypes')->andReturn([
        (object) ['kind' => TypeKindEnum::COLLECT_OBJECT]
    ]);

    $result = $this->cast->match(['value1', 'value2'], $this->collection, $this->context);
    expect($result)->toBeTrue();
});

test('resolve correctly maps values for collect object type', function () {
    $child = Mockery::mock(DataGroupCollection::class);
    $childType = (object) ['kind' => TypeKindEnum::COLLECT_OBJECT];

    $this->collection->shouldReceive('getChildren')->andReturn([$child]);
    $this->collection->shouldReceive('getTypes')->andReturn([$childType]);
    $this->collection->shouldReceive('setChooseType')->with($childType);

    $child->shouldReceive('getClassName')->andReturn('SomeClass');

    $values = ['value1', 'value2'];

    // Mock SerializeContainer 和 propertyInputValueResolver
    $resolverMock = Mockery::mock(PropertyInputValueResolver::class);
    $this->serializeContainerMock->shouldReceive('propertyInputValueResolver')->andReturn($resolverMock);

    $resolverMock->shouldReceive('resolve')->with('SomeClass', $child, 'value1')->andReturn('resolved1');
    $resolverMock->shouldReceive('resolve')->with('SomeClass', $child, 'value2')->andReturn('resolved2');

    $result = $this->cast->resolve($values, $this->collection, $this->context);
    expect($result)->toBe(['resolved1', 'resolved2']);
});


test('resolve returns single resolved value for non-collect object type', function () {
    $child = Mockery::mock(DataGroupCollection::class);
    $childType = (object) ['kind' => TypeKindEnum::SINGLE_OBJECT];

    $this->collection->shouldReceive('getChildren')->andReturn([$child]);
    $this->collection->shouldReceive('getTypes')->andReturn([$childType]);
    $this->collection->shouldReceive('setChooseType')->with($childType);

    $child->shouldReceive('getClassName')->andReturn('SomeClass');

    $resolverMock = Mockery::mock(PropertyInputValueResolver::class);
    SerializeContainer::get()->shouldReceive('propertyInputValueResolver')->andReturn($resolverMock);

    $resolverMock->shouldReceive('resolve')->with('SomeClass', $child, 'value')->andReturn('resolved_value');

    $result = $this->cast->resolve('value', $this->collection, $this->context);
    expect($result)->toBe('resolved_value');
});
