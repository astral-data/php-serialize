<?php

namespace Astral\Serialize\Support\Factories;

use Astral\Serialize\SerializeContainer;
use Astral\Serialize\Support\Context\ChooseSerializeContext;
use Astral\Serialize\Support\Context\SerializeContext;

class ContextFactory
{
    /**
     */
    public static function build(string $className): SerializeContext
    {
        return (new SerializeContext(
            serializeClassName: $className,
            chooseSerializeContext:new ChooseSerializeContext($className),
            cache: CacheFactory::build(),
            reflectionClassInstanceManager: SerializeContainer::get()->reflectionClassInstanceManager(),
            groupResolver: SerializeContainer::get()->groupResolver(),
            dataCollectionCastResolver: SerializeContainer::get()->attributePropertyResolver(),
            constructDataCollectionManager: SerializeContainer::get()->constructDataCollectionManager(),
            propertyInputValueResolver: SerializeContainer::get()->propertyInputValueResolver(),
        ));
    }
}
