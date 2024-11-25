<?php

namespace Astral\Serialize\Support\Caching;

use Astral\Serialize\Support\Collections\DataGroupCollection;

class ClassGroupsCache
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

    public function put(string $className, array $groups): void
    {
        self::$caches[$className] = $groups;
    }


    public function toArray(): array
    {
        return self::$caches;
    }
}
