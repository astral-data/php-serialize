<?php

namespace Astral\Serialize\Resolvers;

use Astral\Serialize\Annotations\Groups;
use ReflectionClass;
use ReflectionProperty;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Mixed_;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\ContextFactory;

class ClassGroupResolver
{

    public function resolveExistsGroups(ReflectionClass|ReflectionProperty $reflection, string|array $groups): bool
    {
        if (!is_array($groups)) {
            $groups = [$groups];
        }

        return $this->resolveExistsGroup($reflection, $groups);
    }

    public function getGroupsTo(ReflectionClass|ReflectionProperty $reflection)
    {
        $reflection->getAttributes(Groups::class);
    }
}
