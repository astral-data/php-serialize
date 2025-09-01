<?php

namespace Astral\Serialize\Support\Config;

use Astral\Serialize\Casts\InputValue\InputArrayBestMatchChildCast;
use Astral\Serialize\Casts\InputValue\InputArraySingleChildCast;
use Astral\Serialize\Casts\InputValue\InputObjectBestMatchChildCast;
use Astral\Serialize\Casts\InputValue\InputValueEnumCast;
use Astral\Serialize\Casts\InputValue\InputValueNullCast;
use Astral\Serialize\Casts\Normalizer\ArrayNormalizerCast;
use Astral\Serialize\Casts\Normalizer\DateTimeNormalizerCast;
use Astral\Serialize\Casts\Normalizer\JsonNormalizerCast;
use Astral\Serialize\Casts\Normalizer\ObjectNormalizerCast;
use Astral\Serialize\Casts\OutValue\OutValueEnumCast;
use Astral\Serialize\Casts\OutValue\OutValueGetterCast;
use Astral\Serialize\Contracts\Attribute\DataCollectionCastInterface;
use Astral\Serialize\Contracts\Attribute\InputValueCastInterface;
use Astral\Serialize\Contracts\Attribute\OutValueCastInterface;
use Astral\Serialize\Contracts\Normalizer\NormalizerCastInterface;
use Astral\Serialize\Enums\CacheDriverEnum;
use Astral\Serialize\Enums\ConfigCastEnum;
use Astral\Serialize\Support\Caching\MemoryCache;
use RuntimeException;

class ConfigManager
{
    public static ConfigManager $instance;

    /** @var DataCollectionCastInterface[] $attributePropertyResolver */
    private array $attributePropertyResolver = [];

    /** @var (InputValueCastInterface|string)[] $inputValueCasts */
    private array $inputValueCasts = [
        InputValueNullCast::class,
        InputObjectBestMatchChildCast::class,
        InputArraySingleChildCast::class,
        InputArrayBestMatchChildCast::class,
        InputValueEnumCast::class,
    ];

    /** @var (OutValueCastInterface|string)[] $outputValueCasts */
    private array $outputValueCasts = [
        OutValueEnumCast::class,
        OutValueGetterCast::class,
    ];

    /** @var (NormalizerCastInterface|string)[] $inputNormalizerCasts */
    private array $inputNormalizerCasts = [
//        JsonNormalizerCast::class,
        ArrayNormalizerCast::class,
    ];

    /** @var (NormalizerCastInterface|string)[] $inputNormalizerCasts */
    private array $outNormalizerCasts = [
        DateTimeNormalizerCast::class,
        ArrayNormalizerCast::class,
        ObjectNormalizerCast::class
    ];

    /** @var CacheDriverEnum|class-string $cacheDriver */
    private string|CacheDriverEnum $cacheDriver = MemoryCache::class;

    public static function getInstance(): ConfigManager
    {
        return self::$instance ??= new self();
    }

    public function __construct()
    {
        $this->instantiateArrayProperties(ConfigCastEnum::getValues());
    }

    /**
     * @param array $propertyNames
     * @return void
     */
    private function instantiateArrayProperties(array $propertyNames): void
    {
        foreach ($propertyNames as $property) {
            if (!property_exists($this, $property)) {
                throw new RuntimeException("Property $property does not exist");
            }

            $this->$property = array_map(
                fn ($class) => is_string($class) ? new $class() : $class,
                $this->$property
            );
        }
    }

    /**
     * @param object|string $castClass
     * @param ConfigCastEnum $castEnum
     * @return static
     * @throws RuntimeException
     */
    public function addCast(object|string $castClass, ConfigCastEnum $castEnum): static
    {
        if (is_string($castClass) && !is_subclass_of($castClass, $castEnum->getCastInterface())) {
            throw new RuntimeException("Cast class must be an instance of {$castEnum->getCastInterface()}");
        }

        if (!property_exists($this, $castEnum->value)) {
            throw new RuntimeException("Property {$castEnum->value} does not exist");
        }

        $this->{$castEnum->value}[] = is_string($castClass) ? new $castClass() : $castClass;

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

    public function getInputNormalizerCasts(): array
    {
        return $this->inputNormalizerCasts;
    }

    public function getOutNormalizerCasts(): array
    {
        return $this->outNormalizerCasts;
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
