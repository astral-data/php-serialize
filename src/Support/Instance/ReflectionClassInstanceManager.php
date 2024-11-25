<?php

namespace Astral\Serialize\Support\Instance;

use ReflectionClass;
use ReflectionException;

class ReflectionClassInstanceManager
{
    private static array $instances = [];

    /**
     * @throws ReflectionException
     */
    public static function get(string $className): ReflectionClass
    {
        if (!isset(self::$instances[$className])) {
            self::$instances[$className] = new ReflectionClass($className);
        }

        return self::$instances[$className];
    }

    public static function clear(): void
    {
        self::$instances = [];
    }
}
