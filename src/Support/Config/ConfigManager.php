<?php

namespace Astral\Serialize\Support\Config;

use Astral\Serialize\Cast\OutValueEnumCast;
use Astral\Serialize\Enums\CacheDriverEnum;
use Astral\Serialize\Support\Caching\MemoryCache;
use Astral\Serialize\Cast\InputValue\InputValueEnumCast;
use Astral\Serialize\Cast\InputValue\InputValueSetTypeCast;
use Astral\Serialize\Contracts\Attribute\OutValueCastInterface;
use Astral\Serialize\Cast\InputValue\InputValueSingleChildCast;
use Astral\Serialize\Contracts\Attribute\InputValueCastInterface;
use Astral\Serialize\Exceptions\NotFoundAttributePropertyResolver;
use Astral\Serialize\Cast\InputValue\InputValueBestMatchChildCast;
use Astral\Serialize\Contracts\Attribute\DataCollectionCastInterface;

class ConfigManager
{
    public static ConfigManager $instance;

    /** @var DataCollectionCastInterface[] $attributePropertyResolver */
    private array $attributePropertyResolver = [];

    /** @var InputValueCastInterface[] $inputValueCasts */
    private array $inputValueCasts = [
        InputValueSingleChildCast::class,
        InputValueBestMatchChildCast::class,
        InputValueSetTypeCast::class,
        InputValueEnumCast::class,
    ];

    /** @var OutValueCastInterface[] $outputValueCasts */
    private array $outputValueCasts = [
        OutValueEnumCast::class
    ];

    /** @var CacheDriverEnum|class-string $cacheDriver */
    private string|CacheDriverEnum $cacheDriver = MemoryCache::class;

    public function __construct()
    {
        foreach ($this->inputValueCasts as $key => $cast) {
            $this->inputValueCasts[$key] = new $cast();
        }

        foreach ($this->outputValueCasts as $key => $cast) {
            $this->outputValueCasts[$key] = new $cast();
        }

    }

    public static function getInstance(): ConfigManager
    {
        return self::$instance ??= new self();
    }

    /**
     * @throws NotFoundAttributePropertyResolver
     */
    public function addAttributePropertyResolver(DataCollectionCastInterface|string $resolverClass): static
    {
        if (is_string($resolverClass) && !is_subclass_of($resolverClass, DataCollectionCastInterface::class)) {
            throw new NotFoundAttributePropertyResolver('Resolver class must be an instance of DataCollectionCastInterface');
        }
        $this->attributePropertyResolver[] = (is_string($resolverClass) ? new $resolverClass() : $resolverClass);

        return $this;
    }

    /**
     * @throws NotFoundAttributePropertyResolver
     */
    public function addOutputValueCasts(OutValueCastInterface|string $castClass): static
    {
        if (is_string($castClass) && !is_subclass_of($castClass, OutValueCastInterface::class)) {
            throw new NotFoundAttributePropertyResolver('Resolver class must be an instance of OutValueCastInterface');
        }
        $this->outputValueCasts[] = (is_string($castClass) ? new $castClass() : $castClass);

        return $this;
    }

    /**
     * @throws NotFoundAttributePropertyResolver
     */
    public function addInputValueCasts(InputValueCastInterface|string $castClass): static
    {
        if (is_string($castClass) && !is_subclass_of($castClass, InputValueCastInterface::class)) {
            throw new NotFoundAttributePropertyResolver('Resolver class must be an instance of InputValueCastInterface');
        }
        $this->inputValueCasts[] = (is_string($castClass) ? new $castClass() : $castClass);

        return $this;
    }

    public function getAttributePropertyResolver(): array
    {
        return $this->attributePropertyResolver;
    }

    public function getInputValueCasts(): array
    {
        return $this->inputValueCasts;
    }

    public function getOutValueCasts(): array
    {
        return $this->outputValueCasts;
    }

    public function getCacheDriver(): string
    {
        if ($this->cacheDriver instanceof CacheDriverEnum) {
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
