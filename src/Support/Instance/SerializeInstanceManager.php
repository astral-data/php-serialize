<?php

namespace Astral\Serialize\Support\Instance;

use InvalidArgumentException;

class SerializeInstanceManager {

    private static array $instances = [];

    public static function build($class)
    {
        if (!class_exists($class)) {
            throw new InvalidArgumentException("Class {$class} does not exist.");
        }

        if(!isset(self::$instances[$class])){
            self::$instances[$class] = new $class;
        }

        return self::$instances[$class];

    }

    public static function clear($class = null): void
    {
        if ($class) {
            unset(self::$instances[$class]);
        } else {
            self::$instances = [];
        }
    }

}