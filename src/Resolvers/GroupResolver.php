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
    public function resolveExistsGroups(ReflectionClass|ReflectionProperty $reflection, string $defaultGroup, string|array $groups): bool
    {

        $groups          = (array) $groups;
        $availableGroups = array_merge([$defaultGroup], $this->getGroupsTo($reflection));
        $invalidGroups   = array_filter($groups, fn ($group) => !in_array($group, $availableGroups, true));

        if ($invalidGroups) {
            throw new NotFoundGroupException(sprintf(
                'Invalid group(s) "%s" for %s. Available groups: [%s]',
                implode(',', $invalidGroups),
                $reflection->getName(),
                implode(',', $availableGroups)
            ));
        }

        return true;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getGroupsTo(ReflectionClass|ReflectionProperty $reflection): array
    {
        $cacheKey = $this->getCacheKey($reflection);
        if ($this->cache->has($cacheKey)) {
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
