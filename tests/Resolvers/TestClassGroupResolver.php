<?php

use Astral\Serialize\Exceptions\NotFoundGroupException;
use Astral\Serialize\Resolvers\GroupResolver;
use Psr\SimpleCache\CacheInterface;

it('throws NotFoundGroupException when groups do not exist', function () {
    $mockCache = mock(CacheInterface::class);
    $mockCache->shouldReceive('has')->andReturnUsing(fn ($key) => false);
    $mockCache->shouldReceive('get')->andReturnUsing(fn ($key) => []);
    $mockCache->shouldReceive('set')->andReturnUsing(fn ($key, $value) => true);

    $resolver = new GroupResolver($mockCache);

    $reflection = $this->createMock(ReflectionClass::class);
    $reflection->method('getAttributes')->willReturn([]);

    $resolver->resolveExistsGroups($reflection, ['nonexistent']);
})->throws(NotFoundGroupException::class);

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
    $result   = $resolver->resolveExistsGroups($reflection, ['group1']);

    expect($result)->toBeTrue();
});

it('returns cached groups when available', function () {
    $mockCache = mock(CacheInterface::class);
    $mockCache->shouldReceive('has')->andReturnUsing(fn ($key) => true);
    $mockCache->shouldReceive('get')->andReturnUsing(fn ($key) => ['cached_group1', 'cached_group2']);

    $resolver = new GroupResolver($mockCache);

    $reflection = $this->createMock(ReflectionClass::class);

    $result = $resolver->resolveExistsGroups($reflection, ['cached_group1']);

    expect($result)->toBeTrue();
});

it('generates correct cache keys for ReflectionClass', function () {
    $mockCache = mock(CacheInterface::class);

    $reflection = $this->createMock(ReflectionClass::class);
    $reflection->method('getName')->willReturn('TestClass');

    $resolver = new GroupResolver($mockCache);
    $cacheKey = $resolver->getCacheKey($reflection);

    expect($cacheKey)->toBe('TestClass');
});

it('generates correct cache keys for ReflectionProperty', function () {
    $mockCache = mock(CacheInterface::class);

    $declaringClass = $this->createMock(ReflectionClass::class);
    $declaringClass->method('__toString')->willReturn('TestClass');

    $reflection = $this->createMock(ReflectionProperty::class);
    $reflection->method('getDeclaringClass')->willReturn($declaringClass);
    $reflection->method('getName')->willReturn('testProperty');

    $resolver = new GroupResolver($mockCache);
    $cacheKey = $resolver->getCacheKey($reflection);

    expect($cacheKey)->toBe('TestClass:testProperty');
});
