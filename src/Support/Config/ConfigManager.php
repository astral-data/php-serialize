<?php

namespace Astral\Serialize\Support\Config;

use Astral\Serialize\Contracts\Attribute\DataCollectionCastInterface;
use Astral\Serialize\Enums\CacheDriverEnum;
use Astral\Serialize\Exceptions\NotFoundAttributePropertyResolver;
use Astral\Serialize\Support\Caching\MemoryCache;
use Illuminate\Support\Collection;

class ConfigManager
{
    public static ConfigManager $instance;

    private Collection $attributePropertyResolver;

    private array $inputValueCasts = [

    ];

    private array $outputValueCasts = [

    ];

    private string|CacheDriverEnum $cacheDriver = MemoryCache::class;

    public static function getInstance(): ConfigManager
    {
        return self::$instance ??= new self();
    }

    /**
     * @throws NotFoundAttributePropertyResolver
     */
    public function addAttributePropertyResolver(DataCollectionCastInterface|string $resolverClass): static
    {
        if(is_string($resolverClass) && !is_subclass_of($resolverClass, DataCollectionCastInterface::class)) {
            throw new NotFoundAttributePropertyResolver('Resolver class must be an instance of DataCollectionCastInterface');
        }

        $this->getAttributePropertyResolver()->push(is_string($resolverClass) ? new $resolverClass() : $resolverClass);
        return $this;
    }

    /**
     * @return Collection
     */
    public function getAttributePropertyResolver(): Collection
    {
        return $this->attributePropertyResolver ??= new Collection();
    }

    public function setTransFromClass($val): static
    {
        $this->transFromClass[$val] = $val;

        return $this;
    }

    public function getTransFromClass(): array
    {
        return $this->transFromClass;
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
