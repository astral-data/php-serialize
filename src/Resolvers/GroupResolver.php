<?php

namespace Astral\Serialize\Resolvers;

use Astral\Serialize\Annotations\Groups;
use Astral\Serialize\Exceptions\NotFoundGroupException;
use Astral\Serialize\Support\Collections\DataCollection;
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
     * @throws InvalidArgumentException
     * @throws NotFoundGroupException
     */
    public function resolveExistsGroupsByClass(ReflectionClass $reflection, string $defaultGroup, array $groups): bool
    {
        $availableGroups = array_merge([$defaultGroup], $this->getDefaultGroups($reflection));
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
     */
    public function resolveExistsGroupsByDataCollection(DataCollection $collection, array $groups, string $defaultGroup): bool
    {
        if (!$collection->getGroups() && count($groups) == 1 && current($groups) === $defaultGroup) {
            return true;
        }

        foreach ($groups as $group) {
            if (in_array($group, $collection->getGroups())) {
                return true;
            }
        }

        return false;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getDefaultGroups(ReflectionClass $reflection): array
    {
        $cacheKey = 'default_groups:' . $reflection->getName();
        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }

        $groups = $this->getGroupsTo($reflection);

        foreach ($reflection->getProperties() as $property) {
            $groups =   array_merge($groups, $this->getGroupsTo($property));
        }

        $groups = array_unique($groups);
        $this->cache->set($cacheKey, $groups);

        return $groups;
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

        $groupNames = $attributes[0]->newInstance()->names;
        $this->cache->set($cacheKey, $groupNames);

        return $groupNames;
    }

    public function getCacheKey(ReflectionClass|ReflectionProperty $reflection): string
    {
        return 'group:' . $reflection instanceof ReflectionClass ? $reflection->getName() : $reflection->getDeclaringClass()->getName() . ':' . $reflection->getName();
    }
}
