<?php

namespace Astral\Serialize\Support\Caching;

use Astral\Serialize\Support\Collections\DataGroupCollection;

class GlobalDataCollectionCache
{
    private static array $caches = [];

    public static function has(string $className, string $groupName): bool
    {
        return isset(self::$caches[self::getCacheKey($className, $groupName)]);
    }

    public static function &get(string $className, string $groupName): ?DataGroupCollection
    {
        $cacheKey = self::getCacheKey($className, $groupName);

        if(isset(self::$caches[$cacheKey])){
            return self::$caches[$cacheKey];
        }

        $null = null;
        return  $null;
    }

    public static function put(string $className, string $groupName, DataGroupCollection $collection): void
    {
        self::$caches[self::getCacheKey($className, $groupName)] = $collection;
    }

    private static function getCacheKey(string $className, string $groupName): string
    {
        return sha1($className . '_' . $groupName);
    }
}
