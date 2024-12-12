<?php

namespace Astral\Serialize\Support\Factories;

use Astral\Serialize\Context;
use Astral\Serialize\SerializeContainer;

class ContextFactory
{
    /**
     */
    public static function build(string $className): Context
    {
        return (new Context(
            CacheFactory::build(),
            SerializeContainer::get()->reflectionClassInstanceManager(),
            SerializeContainer::get()->classGroupResolver(),
            SerializeContainer::get()->attributePropertyResolver()
        ))
        ->setClassName($className);
    }
}
