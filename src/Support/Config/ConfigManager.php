<?php

namespace Astral\Serialize\Support\Config;

use Astral\Serialize\Enums\CacheDriverEnum;
use Astral\Serialize\Support\Caching\MemoryCache;

class ConfigManager
{
    public static ConfigManager $instance;
    private array $inputTransFrom = [];
    private array $outTransFrom   = [];

    private string|CacheDriverEnum $cacheDriver = MemoryCache::class;

    public static function getInstance(): ConfigManager
    {
        return self::$instance ??= new self();
    }

    public function addInputTransFrom($val): static
    {
        $this->inputTransFrom[] = $val;
        return $this;
    }

    public function addOutTransFrom($val): static
    {
        $this->outTransFrom[] = $val;
        return $this;
    }

    public function getInputTransFrom(): array
    {
        return $this->inputTransFrom;
    }

    public function getOutTransFrom(): array
    {
        return $this->outTransFrom;
    }

    public function getCacheDriver(): string
    {
        if($this->cacheDriver instanceof CacheDriverEnum) {
            return $this->cacheDriver->value;
        }

        return $this->cacheDriver;
    }

    public function setCacheDriver(string|CacheDriverEnum $cacheDriver): static
    {
        $this->cacheDriver = $cacheDriver;
        return $this;
    }
}
