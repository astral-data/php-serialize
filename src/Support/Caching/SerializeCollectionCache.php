<?php

namespace Astral\Serialize\Support\Caching;

use Astral\Serialize\Support\Collections\DataGroupCollection;

class SerializeCollectionCache
{
    private static array $caches = [];

    public function has(string $className): bool
    {
        return isset(self::$caches[$className]);
    }

    public function get(string $className): ?DataGroupCollection
    {
        $cacheKey = $className;

        if (isset(self::$caches[$cacheKey])) {
            return self::$caches[$cacheKey];
        }

        return null;
    }

    public function put(string $className, DataGroupCollection $collection): void
    {
        self::$caches[$className] = $collection;
    }

    public function toArray(): array
    {
        return self::$caches;
    }
}
