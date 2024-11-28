<?php

namespace Astral\Serialize\Support\Instance;

use ReflectionClass;
use ReflectionException;

class ReflectionClassInstanceManager
{
    private array $instances = [];

    /**
     * @throws ReflectionException
     */
    public function get(string $className): ReflectionClass
    {
        if (!isset($this->instances[$className])) {
            $this->instances[$className] = new ReflectionClass($className);
        }

        return$this->instances[$className];
    }

    public function clear(): void
    {
        $this->instances = [];
    }
}
