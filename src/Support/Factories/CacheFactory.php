<?php

namespace Astral\Serialize\Support\Factories;

use Astral\Serialize\Context;
use Psr\SimpleCache\CacheInterface;
use Astral\Serialize\SerializeContainer;
use Astral\Serialize\Support\Facades\Bootstrap;

class CacheFactory
{
    public static function build(): CacheInterface
    {
        return new (Bootstrap::getCacheDriver());
    }
}
