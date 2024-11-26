<?php

namespace Astral\Serialize\Enums;

use Astral\Serialize\Support\Caching\MemoryCache;
use Astral\Serialize\Support\Caching\LaravelCache;

enum CacheDriverEnum: string
{
    case LARAVEL = LaravelCache::class;
    case MEMORY = MemoryCache::class;
}
