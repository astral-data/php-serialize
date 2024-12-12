<?php

namespace Astral\Serialize\Resolvers;

use Astral\Serialize\Annotations\Groups;
use Astral\Serialize\Exceptions\NotFoundGroupException;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use ReflectionClass;
use ReflectionProperty;

class GroupResolver
{
    public function __construct(
        private readonly CacheInterface $cache
    ) {

    }

    /**
     * @throws NotFoundGroupException
     * @throws InvalidArgumentException
     */
    public function resolveExistsGroups(ReflectionClass|ReflectionProperty $reflection, string|array $groups): bool
    {
        $groups          = (array) $groups;
        $defaultGroup    =  $reflection instanceof ReflectionProperty ? $reflection->getDeclaringClass()->getName() : $reflection->getName();
        $availableGroups = array_merge([$defaultGroup], $this->getGroupsTo($reflection));

        if(!empty(array_diff($groups, $availableGroups))) {
            throw new NotFoundGroupException(
                sprintf('Invalid group(s) "%s" for %s', implode(',', array_diff($groups, $availableGroups)), $reflection->getName())
            );
        }

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

        return $attributes[0]->newInstance()->names;
    }

    public function getCacheKey(ReflectionClass|ReflectionProperty $reflection): string
    {
        return $reflection instanceof ReflectionClass ? $reflection->getName() : $reflection->getDeclaringClass() . ':' . $reflection->getName();
    }
}
