<?php

namespace Astral\Serialize\Support\Factories;

use Astral\Serialize\Context;
use Astral\Serialize\SerializeContainer;

class ContextFactory
{
    /**
     */
    public static function build(string $className, object $object): Context
    {
        return (new Context(
            serialize:$object,
            serializeClassName:$className,
            cache:CacheFactory::build(),
            reflectionClassInstanceManager:SerializeContainer::get()->reflectionClassInstanceManager(),
            classGroupResolver:SerializeContainer::get()->classGroupResolver(),
            dataCollectionCastResolver:SerializeContainer::get()->attributePropertyResolver(),
            propertyInputValueResolver:SerializeContainer::get()->propertyInputValueResolver()
        ));
    }
}
