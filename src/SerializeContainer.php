<?php

namespace Astral\Serialize;

use Astral\Serialize\Resolvers\ClassGroupResolver;
use Astral\Serialize\Resolvers\PropertyTypeDocResolver;
use Astral\Serialize\Resolvers\PropertyTypesContextResolver;
use Astral\Serialize\Support\Collections\TypeCollectionManager;
use Astral\Serialize\Support\Instance\ReflectionClassInstanceManager;
use Astral\Serialize\Support\Instance\ReflectionContextInstanceManager;
use Astral\Serialize\Support\Instance\SerializeInstanceManager;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\ContextFactory;

class SerializeContainer
{
    protected static self $instance;
    protected ?ContextFactory $contextFactory = null;

    protected ?Context $context                                                   = null;
    protected ?TypeResolver $typeResolver                                         = null;
    protected ?DocBlockFactory $docBlockFactory                                   = null;
    protected ?TypeCollectionManager $typeCollectionManager                       = null;
    protected ?PropertyTypesContextResolver $propertyTypesContextResolver         = null;
    protected ?PropertyTypeDocResolver $propertyTypeDocResolver                   = null;
    protected ?ClassGroupResolver $classGroupResolver                             = null;
    protected ?ReflectionClassInstanceManager $reflectionClassInstanceManager     = null;
    protected ?ReflectionContextInstanceManager $reflectionContextInstanceManager = null;
    protected ?SerializeInstanceManager $serializeInstanceManager                 = null;

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

    public function classGroupResolver(): ClassGroupResolver
    {
        return $this->classGroupResolver ??= new ClassGroupResolver();
    }

    public function reflectionClassInstanceManager(): ReflectionClassInstanceManager
    {
        return $this->reflectionClassInstanceManager ??= new ReflectionClassInstanceManager();
    }

    public function reflectionContextInstanceManager(): ReflectionContextInstanceManager
    {
        return $this->reflectionContextInstanceManager ??= new ReflectionContextInstanceManager();
    }

    public function serializeInstanceManager(): SerializeInstanceManager
    {
        return $this->serializeInstanceManager ??= new SerializeInstanceManager();
    }

    public function context(): Context
    {
        return $this->context ??= new Context($this->classGroupResolver(), $this->reflectionClassInstanceManager());
    }
}
