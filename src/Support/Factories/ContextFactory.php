<?php

namespace Astral\Serialize\Support\Factories;

use Astral\Serialize\Context;
use Astral\Serialize\SerializeContainer;

class ContextFactory
{
    public static function build(string $className, array $groups): Context
    {
        return (new Context(
            SerializeContainer::get()->classGroupResolver(),
            SerializeContainer::get()->reflectionClassInstanceManager(),
            CacheFactory::build()
        ))
        ->setClassName($className)
        ->setGroups($groups);
    }
}
