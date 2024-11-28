<?php

namespace Astral\Serialize\Support\Instance;

use InvalidArgumentException;

class SerializeInstanceManager
{
    private array $instances = [];

    public function get($class)
    {
        if (!class_exists($class)) {
            throw new InvalidArgumentException("Class {$class} does not exist.");
        }

        if (!isset($this->instances[$class])) {
            $this->instances[$class] = new $class();
        }

        return $this->instances[$class];
    }

    public function clear($class = null): void
    {
        if ($class) {
            unset($this->instances[$class]);
        } else {
            $this->instances = [];
        }
    }
}
