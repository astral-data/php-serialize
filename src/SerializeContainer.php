<?php

namespace Astral\Serialize;

use phpDocumentor\Reflection\TypeResolver;
use Astral\Serialize\Resolvers\GroupResolver;
use phpDocumentor\Reflection\DocBlockFactory;
use Astral\Serialize\Casts\InputConstructCast;
use Astral\Serialize\Support\Config\ConfigManager;
use phpDocumentor\Reflection\Types\ContextFactory;
use Astral\Serialize\Support\Factories\CacheFactory;
use Astral\Serialize\Resolvers\InputValueCastResolver;
use Astral\Serialize\Support\Context\SerializeContext;
use Astral\Serialize\Resolvers\PropertyTypeDocResolver;
use Astral\Serialize\Resolvers\DataCollectionCastResolver;
use Astral\Serialize\Resolvers\PropertyInputValueResolver;
use Astral\Serialize\Resolvers\PropertyTypesContextResolver;
use Astral\Serialize\Support\Instance\SerializeInstanceManager;
use Astral\Serialize\Casts\InputValue\InputArraySingleChildCast;
use Astral\Serialize\Support\Instance\ReflectionClassInstanceManager;
use Astral\Serialize\Support\Collections\Manager\TypeCollectionManager;
use Astral\Serialize\Support\Collections\Manager\ConstructDataCollectionManager;

class SerializeContainer
{
    protected static self $instance;
    protected ?ContextFactory $contextFactory                                               = null;
    protected ?SerializeContext $context                                                    = null;
    protected ?TypeResolver $typeResolver                                                   = null;
    protected ?DocBlockFactory $docBlockFactory                                             = null;
    protected ?TypeCollectionManager $typeCollectionManager                                 = null;
    protected ?PropertyTypesContextResolver $propertyTypesContextResolver                   = null;
    protected ?PropertyTypeDocResolver $propertyTypeDocResolver                             = null;
    protected ?GroupResolver $classGroupResolver                                            = null;
    protected ?ReflectionClassInstanceManager $reflectionClassInstanceManager               = null;
    protected ?SerializeInstanceManager $serializeInstanceManager                           = null;
    protected ?DataCollectionCastResolver $attributePropertyResolver                        = null;

    protected ?PropertyInputValueResolver $propertyInputValueResolver = null;

    protected ?InputValueCastResolver $inputValueCastResolver               = null;

    protected ?InputConstructCast $inputConstructCast = null;

    protected ?ConstructDataCollectionManager $constructDataCollectionManager = null;

    public static function get(): SerializeContainer
    {
        return static::$instance ??= new self();
    }

    public function contextFactory(): ContextFactory
    {
        return $this->contextFactory ??= new ContextFactory();
    }

    public function typeResolver(): TypeResolver
    {
        return $this->typeResolver ??= new TypeResolver();
    }

    public function docBlockFactory(): DocBlockFactory
    {
        return $this->docBlockFactory ??= DocBlockFactory::createInstance();
    }

    public function propertyTypesContextResolver(): PropertyTypesContextResolver
    {
        return $this->propertyTypesContextResolver ??= new PropertyTypesContextResolver();
    }

    public function propertyTypeDocResolver(): PropertyTypeDocResolver
    {
        return $this->propertyTypeDocResolver ??= new PropertyTypeDocResolver();
    }

    public function typeCollectionManager(): TypeCollectionManager
    {
        return $this->typeCollectionManager ??= new TypeCollectionManager(
            $this->propertyTypeDocResolver(),
            $this->propertyTypesContextResolver(),
            $this->typeResolver()
        );
    }

    public function classGroupResolver(): GroupResolver
    {
        return $this->classGroupResolver ??= new GroupResolver(
            CacheFactory::build()
        );
    }

    public function attributePropertyResolver(): DataCollectionCastResolver
    {
        return $this->attributePropertyResolver ??= new DataCollectionCastResolver(ConfigManager::getInstance());
    }

    public function reflectionClassInstanceManager(): ReflectionClassInstanceManager
    {
        return $this->reflectionClassInstanceManager ??= new ReflectionClassInstanceManager();
    }

    public function propertyInputValueResolver(): PropertyInputValueResolver
    {
        return $this->propertyInputValueResolver ??= new PropertyInputValueResolver(
            reflectionClassInstanceManager:$this->reflectionClassInstanceManager(),
            inputValueCastResolver:$this->inputValueCastResolver(),
            inputConstructCast:$this->inputConstructCast(),
        );
    }

    public function inputValueCastResolver(): InputValueCastResolver
    {
        return $this->inputValueCastResolver ??= new InputValueCastResolver(ConfigManager::getInstance());
    }

    public function inputConstructCast(): InputConstructCast
    {
        return $this->inputConstructCast ??= new InputConstructCast();
    }

    public function constructDataCollectionManager(): ConstructDataCollectionManager
    {
        return $this->constructDataCollectionManager ??= new ConstructDataCollectionManager();
    }

    public function serializeInstanceManager(): SerializeInstanceManager
    {
        return $this->serializeInstanceManager ??= new SerializeInstanceManager();
    }
}
