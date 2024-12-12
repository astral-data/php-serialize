<?php

namespace Astral\Serialize\Support\Facades;

use Astral\Serialize\Enums\CacheDriverEnum;
use Astral\Serialize\Support\Config\ConfigManager;
use BadMethodCallException;

/**
 * @method static string|CacheDriverEnum getCacheDriver()
 * @see ConfigManager::getCacheDriver
 */
class Bootstrap
{
    public static function __callStatic(string $name, array $arguments): Bootstrap|string
    {
        $instance = ConfigManager::getInstance();

        if (!method_exists($instance, $name)) {
            throw new BadMethodCallException("Method {$name} does not exist on ConfigManager.");
        }

        $result = $instance->$name(...$arguments);

        return $result instanceof ConfigManager ? new self() : $result;
    }
}
