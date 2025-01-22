<?php

use Astral\Serialize\Exceptions\NotFoundGroupException;
use Astral\Serialize\Resolvers\GroupResolver;
use Astral\Serialize\Support\Collections\DataCollection;
use Psr\SimpleCache\CacheInterface;

it('throws NotFoundGroupException when groups do not exist', function () {
    $mockCache = mock(CacheInterface::class);
    $mockCache->shouldReceive('has')->andReturnUsing(fn ($key) => false);
    $mockCache->shouldReceive('get')->andReturnUsing(fn ($key) => []);
    $mockCache->shouldReceive('set')->andReturnUsing(fn ($key, $value) => true);

    $resolver = new GroupResolver($mockCache);

    $reflection = $this->createMock(ReflectionClass::class);
    $reflection->method('getAttributes')->willReturn([]);

    $result = $resolver->resolveExistsGroupsByClass($reflection, 'test', ['nonexistent']);

})->throws(NotFoundGroupException::class, 'Invalid group(s) "nonexistent" for . Available groups: [test]');

it('returns true when groups exist', function () {
    $mockCache = mock(CacheInterface::class);
    $mockCache->shouldReceive('has')->andReturnUsing(fn ($key) => false);
    $mockCache->shouldReceive('set')->andReturnUsing(fn ($key, $value) => true);

    $mockAttribute = new class () {
        public array $names = ['group1', 'group2'];
    };

    $reflection = $this->createMock(ReflectionClass::class);
    $reflection->method('getAttributes')->willReturn([
        new class ($mockAttribute) {
            private $attribute;

            public function __construct($attribute)
            {
                $this->attribute = $attribute;
            }

            public function newInstance()
            {
                return $this->attribute;
            }
        }
    ]);

    $resolver = new GroupResolver($mockCache);
    $result   = $resolver->resolveExistsGroupsByClass($reflection, 'test', ['group1']);

    expect($result)->toBeTrue();
});

it('returns cached groups when available', function () {
    $mockCache = mock(CacheInterface::class);
    $mockCache->shouldReceive('has')->andReturnUsing(fn ($key) => true);
    $mockCache->shouldReceive('get')->andReturnUsing(fn ($key) => ['cached_group1', 'cached_group2']);

    $resolver   = new GroupResolver($mockCache);
    $reflection = $this->createMock(ReflectionClass::class);
    $result     = $resolver->resolveExistsGroupsByClass($reflection, 'test', ['cached_group1']);

    expect($result)->toBeTrue();
});

it('generates correct cache keys for ReflectionClass', function () {
    $mockCache = mock(CacheInterface::class);

    $reflection = $this->createMock(ReflectionClass::class);
    $reflection->method('getName')->willReturn('TestClass');

    $resolver = new GroupResolver($mockCache);
    $cacheKey = $resolver->getCacheKey($reflection);
    expect($cacheKey)->toBe('group:TestClass');
});

it('generates correct cache keys for ReflectionProperty', function () {
    $mockCache = mock(CacheInterface::class);

    $declaringClass = $this->createMock(ReflectionClass::class);
    $declaringClass->method('getName')->willReturn('TestClass');

    $reflection = $this->createMock(ReflectionProperty::class);
    $reflection->method('getDeclaringClass')->willReturn($declaringClass);
    $reflection->method('getName')->willReturn('testProperty');

    $resolver = new GroupResolver($mockCache);
    $cacheKey = $resolver->getCacheKey($reflection);
    expect($cacheKey)->toBe('group:TestClass:testProperty');
});

it('returns true when default group matches in DataCollection', function () {
    $mockCache = mock(CacheInterface::class);

    $collection = $this->createMock(DataCollection::class);
    $collection->method('getGroups')->willReturn([]);

    $resolver = new GroupResolver($mockCache);
    $result   = $resolver->resolveExistsGroupsByDataCollection($collection, ['defaultGroup'], 'defaultGroup');

    expect($result)->toBeTrue();
});

it('returns true when group exists in DataCollection', function () {
    $mockCache = mock(CacheInterface::class);

    $collection = $this->createMock(DataCollection::class);
    $collection->method('getGroups')->willReturn(['group1', 'group2']);

    $resolver = new GroupResolver($mockCache);
    $result   = $resolver->resolveExistsGroupsByDataCollection($collection, ['group2'], 'defaultGroup');

    expect($result)->toBeTrue();
});

it('returns false when group does not exist in DataCollection', function () {
    $mockCache = mock(CacheInterface::class);

    $collection = $this->createMock(DataCollection::class);
    $collection->method('getGroups')->willReturn(['group1', 'group2']);

    $resolver = new GroupResolver($mockCache);
    $result   = $resolver->resolveExistsGroupsByDataCollection($collection, ['nonexistentGroup'], 'defaultGroup');

    expect($result)->toBeFalse();
});
