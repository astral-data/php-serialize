<?php

namespace Astral\Serialize\Support\Factories;

use Astral\Serialize\Support\Facades\Bootstrap;
use Psr\SimpleCache\CacheInterface;

class CacheFactory
{
    private static ?CacheInterface $instance = null;
    public static function build(): CacheInterface
    {
        return self::$instance ??= new (Bootstrap::getCacheDriver());
    }
}
