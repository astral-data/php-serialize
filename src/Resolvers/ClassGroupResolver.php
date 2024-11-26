<?php

namespace Astral\Serialize\Resolvers;

use Astral\Serialize\Context;
use Psr\SimpleCache\CacheInterface;
use Astral\Serialize\Annotations\Groups;
use ReflectionClass;
use ReflectionProperty;
use Psr\SimpleCache\InvalidArgumentException;
use Astral\Serialize\Exceptions\NotFindGroupException;

class ClassGroupResolver
{
    public function __construct(
        private readonly CacheInterface $cache
    ) {

    }


    /**
     * @throws NotFindGroupException
     * @throws InvalidArgumentException
     */
    public function resolveExistsGroups(ReflectionClass|ReflectionProperty $reflection, string|array $groups): bool
    {
        $groups = (array) $groups;
        $availableGroups = array_merge([Context::DEFAULT_GROUP_NAME], $this->getGroupsTo($reflection));
        if(!empty(array_diff($groups, $availableGroups))) {
            throw new NotFindGroupException(
                sprintf('Invalid group(s) "%s" for %s', implode(',', array_diff($groups, $availableGroups)), $reflection->getName())
            );
        };

        return true;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getGroupsTo(ReflectionClass|ReflectionProperty $reflection)
    {
        $cacheKey = $this->getCacheKey($reflection);
        if($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        $attributes = $reflection->getAttributes(Groups::class);
        if (empty($attributes)) {
            $this->cache->set($cacheKey, []);
            return [];
        }

        $this->cache->set($cacheKey, $attributes[0]->newInstance()->names);

        return $attributes;
    }

    public function getCacheKey(ReflectionClass|ReflectionProperty $reflection): string
    {
        return $reflection instanceof ReflectionClass ? $reflection->getName() : $reflection->getDeclaringClass().':'.$reflection->getName();
    }
}
