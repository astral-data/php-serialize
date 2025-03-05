<?php

namespace Astral\Serialize;

use Astral\Serialize\Casts\InputConstructCast;
use Astral\Serialize\Faker\FakerCastResolver;
use Astral\Serialize\Faker\FakerResolver;
use Astral\Serialize\Faker\Rule\FakerDefaultRules;
use Astral\Serialize\Resolvers\Casts\DataCollectionCastResolver;
use Astral\Serialize\Resolvers\Casts\InputValueCastResolver;
use Astral\Serialize\Resolvers\Casts\OutputCastResolver;
use Astral\Serialize\Resolvers\GroupResolver;
use Astral\Serialize\Resolvers\InputResolver;
use Astral\Serialize\Resolvers\OutputResolver;
use Astral\Serialize\Resolvers\PropertyTypeDocResolver;
use Astral\Serialize\Resolvers\PropertyTypesContextResolver;
use Astral\Serialize\Support\Collections\Manager\ConstructDataCollectionManager;
use Astral\Serialize\Support\Collections\Manager\TypeCollectionManager;
use Astral\Serialize\Support\Config\ConfigManager;
use Astral\Serialize\Support\Context\SerializeContext;
use Astral\Serialize\Support\Factories\CacheFactory;
use Astral\Serialize\Support\Instance\ReflectionClassInstanceManager;
use Astral\Serialize\Support\Instance\SerializeInstanceManager;
use Faker\Factory;
use Faker\Generator;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\ContextFactory;

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
    protected ?GroupResolver $groupResolver                                                 = null;
    protected ?ReflectionClassInstanceManager $reflectionClassInstanceManager               = null;
    protected ?SerializeInstanceManager $serializeInstanceManager                           = null;
    protected ?DataCollectionCastResolver $attributePropertyResolver                        = null;
    protected ?InputResolver $propertyInputValueResolver                                    = null;
    protected ?OutputResolver $propertyToArrayResolver                                      = null;
    protected ?InputValueCastResolver $inputValueCastResolver                               = null;
    protected ?OutputCastResolver $outValueCastResolver                                     = null;
    protected ?InputConstructCast $inputConstructCast                                       = null;
    protected ?ConstructDataCollectionManager $constructDataCollectionManager               = null;
    protected ?Generator $faker                                                             = null;
    protected ?FakerResolver $fakerResolver                                                 = null;
    protected ?FakerCastResolver $fakerCastResolver                                         = null;
    protected ?FakerDefaultRules  $fakerDefaultRules                                        = null;

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

    public function groupResolver(): GroupResolver
    {
        return $this->groupResolver ??= new GroupResolver(
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

    public function propertyInputValueResolver(): InputResolver
    {
        return $this->propertyInputValueResolver ??= new InputResolver(
            reflectionClassInstanceManager:$this->reflectionClassInstanceManager(),
            inputValueCastResolver:$this->inputValueCastResolver(),
            inputConstructCast:$this->inputConstructCast(),
            groupResolver: $this->groupResolver(),
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

    public function propertyToArrayResolver(): OutputResolver
    {
        return $this->propertyToArrayResolver ??= new OutputResolver(
            reflectionClassInstanceManager:$this->reflectionClassInstanceManager(),
            outValueCastResolver:$this->outValueCastResolver(),
            groupResolver: $this->groupResolver(),
        );
    }

    public function outValueCastResolver(): OutputCastResolver
    {
        return $this->outValueCastResolver ??= new OutputCastResolver(ConfigManager::getInstance());
    }

    public function serializeInstanceManager(): SerializeInstanceManager
    {
        return $this->serializeInstanceManager ??= new SerializeInstanceManager();
    }

    public function faker(): Generator
    {
        $locale = 'en_US';
        return $this->faker ??= Factory::create($locale);
    }

    public function fakerResolver(): FakerResolver
    {
        return $this->fakerResolver ??= new FakerResolver(
            reflectionClassInstanceManager:$this->reflectionClassInstanceManager(),
            fakerDefaultRules:$this->fakerDefaultRules(),
            fakerCastResolver:$this->fakerCastResolver(),
            inputConstructCast:$this->inputConstructCast(),
            groupResolver: $this->groupResolver(),
        );
    }

    public function fakerCastResolver(): FakerCastResolver
    {
        return $this->fakerCastResolver ??= new FakerCastResolver(ConfigManager::getInstance());
    }

    public function fakerDefaultRules(): FakerDefaultRules
    {
        return $this->fakerDefaultRules ??= new FakerDefaultRules($this->faker());
    }
}
