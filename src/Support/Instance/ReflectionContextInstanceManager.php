<?php

namespace Astral\Serialize\Support\Instance;

use ReflectionClass;
use ReflectionException;

class ReflectionContextInstanceManager
{
    private static array $instances = [];

    /**
     * @throws ReflectionException
     */
    public function get(string $className): ReflectionClass
    {
        if (!isset(self::$instances[$className])) {
            self::$instances[$className] = new ReflectionClass($className);
        }

        return self::$instances[$className];
    }

    public function clear(): void
    {
        self::$instances = [];
    }
}
