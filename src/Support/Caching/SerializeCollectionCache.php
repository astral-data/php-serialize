<?php

namespace Astral\Serialize\Support\Caching;

use Astral\Serialize\Support\Collections\DataGroupCollection;

class SerializeCollectionCache
{

    private static array $caches = [];

    public static function has(string $className): bool
    {
        return isset(self::$caches[$className]);
    }

    public static function get(string $className): ?DataGroupCollection
    {
        $cacheKey = $className;

        if (isset(self::$caches[$cacheKey])) {
            return self::$caches[$cacheKey];
        }

        $null = null;
        return  $null;
    }

    public static function put(string $className, DataGroupCollection $collection): void
    {
        self::$caches[$className] = &$collection;
    }


    public static function toArray()
    {
        return self::$caches;
    }
}
