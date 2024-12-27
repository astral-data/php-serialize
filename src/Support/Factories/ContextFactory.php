<?php

namespace Astral\Serialize\Support\Factories;

use Astral\Serialize\SerializeContainer;
use Astral\Serialize\Support\Context\SerializeContext;
use Astral\Serialize\Support\Collections\Manager\ConstructDataCollectionManager;

class ContextFactory
{
    /**
     */
    public static function build(string $className, ?object $object = null): SerializeContext
    {
        return (new SerializeContext(
//            serialize:$object,
            serializeClassName: $className,
            cache: CacheFactory::build(),
            reflectionClassInstanceManager: SerializeContainer::get()->reflectionClassInstanceManager(),
            classGroupResolver: SerializeContainer::get()->classGroupResolver(),
            dataCollectionCastResolver: SerializeContainer::get()->attributePropertyResolver(),
            constructDataCollectionManager: SerializeContainer::get()->constructDataCollectionManager(),
            propertyInputValueResolver: SerializeContainer::get()->propertyInputValueResolver(),
        ));
    }
}
