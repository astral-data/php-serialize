<?php

namespace Astral\Serialize\Support\Config;

use Astral\Serialize\Annotations\InputIgnore;
use Astral\Serialize\Annotations\InputName;
use Astral\Serialize\Annotations\OutIgnore;
use Astral\Serialize\Annotations\OutName;
use Astral\Serialize\Contracts\Attribute\DataCollectionCastInterface;
use Astral\Serialize\Enums\CacheDriverEnum;
use Astral\Serialize\Exceptions\NotFoundAttributePropertyResolver;
use Astral\Serialize\Support\Caching\MemoryCache;
use Astral\Serialize\Support\TransFrom\InputIgnoreResolver;
use Astral\Serialize\Support\TransFrom\InputNameResolver;
use Astral\Serialize\Support\TransFrom\OutIgnoreResolver;
use Astral\Serialize\Support\TransFrom\OutNameResolver;

class ConfigManager
{
    public static ConfigManager $instance;

    /** @var array<class-string,class-string> */
    private array $attributePropertyResolver = [
        InputIgnore::class => InputIgnoreResolver::class,
        InputName::class   => InputNameResolver::class,
        OutIgnore::class   => OutIgnoreResolver::class,
        OutName::class     => OutNameResolver::class,
    ];

    private array $inputTransFromClass = [

    ];

    private array $outputTransFromClass = [

    ];

    private string|CacheDriverEnum $cacheDriver = MemoryCache::class;

    public static function getInstance(): ConfigManager
    {
        return self::$instance ??= new self();
    }

    public function addAttributePropertyResolver($annotationClass, $resolverClass): static
    {
        $this->attributePropertyResolver[$annotationClass] = $resolverClass;
        return $this;
    }

    /**
     * @return array<class-string,class-string>
     */
    public function getAttributePropertyResolver(): array
    {
        return $this->attributePropertyResolver;
    }

    /**
     * @throws NotFoundAttributePropertyResolver
     */
    public function matchAttributePropertyResolver(string|object $annotationClass): DataCollectionCastInterface
    {
        if (is_object($annotationClass)) {
            $annotationClass = get_class($annotationClass);
        }

        if (isset($this->getAttributePropertyResolver()[$annotationClass])) {
            return new ($this->getAttributePropertyResolver()[$annotationClass]);
        }

        throw  new NotFoundAttributePropertyResolver('not find resolver class:' . $annotationClass);
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
