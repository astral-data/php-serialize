<?php

namespace Astral\Serialize\Support\Factories;

use ReflectionException;
use Astral\Serialize\Context;
use Astral\Serialize\SerializeContainer;
use Psr\SimpleCache\InvalidArgumentException;
use Astral\Serialize\Exceptions\NotFindGroupException;

class ContextFactory
{
    /**
     * @throws ReflectionException
     * @throws NotFindGroupException
     * @throws InvalidArgumentException
     */
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
