<?php

namespace Astral\Serialize\Support\Facades;

use Astral\Serialize\Support\Config\ConfigManager;
use BadMethodCallException;

class Bootstrap {

    public static function __callStatic(string $name, array $arguments)
    {
        $instance = ConfigManager::getInstance();

        if (!method_exists($instance, $name)) {
            throw new BadMethodCallException("Method {$name} does not exist on ConfigManager.");
        }

        $result = $instance->$name(...$arguments);

        return $result instanceof ConfigManager ? new self() : $result;
    }
}