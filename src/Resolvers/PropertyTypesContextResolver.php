<?php

namespace Astral\Serialize\Resolvers;

use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\ContextFactory;
use ReflectionClass;

class PropertyTypesContextResolver
{

    /** @var array<string, Context> */
    protected static array $contexts = [];

    public function execute(ReflectionClass $reflection): Context
    {
        return self::$contexts[$reflection->getName()] ??= (new ContextFactory())->createFromReflector($reflection);
    }
}
