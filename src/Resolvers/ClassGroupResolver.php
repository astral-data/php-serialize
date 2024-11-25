<?php

namespace Astral\Serialize\Resolvers;

use Astral\Serialize\Annotations\Groups;
use ReflectionClass;
use ReflectionProperty;

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
