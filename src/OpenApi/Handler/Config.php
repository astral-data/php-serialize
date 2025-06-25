<?php

declare(strict_types=1);

namespace Astral\Serialize\OpenApi\Handler;

use UnexpectedValueException;

class Config
{
    public static ?array $config = null;

    public static function rootPath(): string
    {
        return dirname(__DIR__, 6);
    }

    public static function build()
    {
        if (self::$config) {
            return self::$config;
        }

        self::$config    = include dirname(__DIR__, 3) . '/.openapi.php';
        $localConfigPath = self::rootPath() . '/.openapi.php';
        if (is_file($localConfigPath)) {
            $localConfig = include $localConfigPath;
            if (!is_array($localConfig)) {
                throw new UnexpectedValueException('Local config must return an array.');
            }
            self::$config = array_merge(self::$config, $localConfig);
        }

        return self::$config;
    }

    public static function get($key, $default = '')
    {
        return self::build()[$key] ?? $default;
    }

    public static function has($key): bool
    {
        return isset(self::build()[$key]);
    }
}
