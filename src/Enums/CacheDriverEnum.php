<?php

namespace Astral\Serialize\Enums;

use Astral\Serialize\Support\Caching\LaravelCache;
use Astral\Serialize\Support\Caching\MemoryCache;

enum CacheDriverEnum: string
{
    case LARAVEL = LaravelCache::class;
    case MEMORY  = MemoryCache::class;
}
