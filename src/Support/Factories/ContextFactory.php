<?php

namespace Astral\Serialize\Support\Factories;

use Astral\Serialize\SerializeContainer;
use Astral\Serialize\Support\Context\SerializeContext;
use Astral\Serialize\Support\Context\ChooseSerializeContext;

class ContextFactory
{
    /**
     */
    public static function build(string $className, ?object $object = null): SerializeContext
    {
        return (new SerializeContext(
            serializeClassName: $className,
            chooseSerializeContext:new ChooseSerializeContext(),
            cache: CacheFactory::build(),
            reflectionClassInstanceManager: SerializeContainer::get()->reflectionClassInstanceManager(),
            groupResolver: SerializeContainer::get()->groupResolver(),
            dataCollectionCastResolver: SerializeContainer::get()->attributePropertyResolver(),
            constructDataCollectionManager: SerializeContainer::get()->constructDataCollectionManager(),
            propertyInputValueResolver: SerializeContainer::get()->propertyInputValueResolver(),
        ));
    }
}
