<?php

namespace Astral\Serialize;

use Astral\Serialize\Resolvers\ClassGroupResolver;
use Astral\Serialize\Support\Caching\GlobalDataCollectionCache;
use Astral\Serialize\Support\Caching\SerializeCollectionCache;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Collections\DataGroupCollection;
use Astral\Serialize\Support\Instance\ReflectionClassInstanceManager;
use ReflectionException;
use ReflectionProperty;

class Context
{
    private string $serializeClassName;
    private array $groups;
    public const DEFAULT_GROUP_NAME = 'default';

    public function __construct(
        public readonly ClassGroupResolver $classGroupResolver,
        public readonly ReflectionClassInstanceManager $reflectionClassInstanceManager
    ) {
    }

    public function setClassName($className): static
    {
        $this->serializeClassName = $className;
        return $this;
    }

    public function setGroups(array $groups): static
    {
        $reflectionClass = $this->reflectionClassInstanceManager->get($this->serializeClassName);
        $this->classGroupResolver->resolveExistsGroup($reflectionClass, $groups);
        $this->groups = $groups;
        return $this;
    }

    public function getGroups(): array
    {
        $this->groups = $this->groups ? $this->groups : [self::DEFAULT_GROUP_NAME];
        return $this->groups;
    }

    /**
     * @throws ReflectionException
     */
    public function getSerializeCollection(): DataGroupCollection
    {
        if (SerializeCollectionCache::has($this->serializeClassName)) {
            return SerializeCollectionCache::get($this->serializeClassName);
        }

        return $this->getGroupCollection();
    }

    /**
     * @throws ReflectionException
     */
    public function getGroupCollection(): DataGroupCollection|array
    {
        $dates = [];
        foreach ($this->groups as $group) {
            if (!GlobalDataCollectionCache::has($this->serializeClassName, $group)) {
                $dates[] = $this->parseSerializeClass($group, $this->serializeClassName);
            }
        }

        return $dates;
    }

    /**
     * @throws ReflectionException
     */
    public function parseSerializeClass(string $groupName, string $className, int $maxDepth = 10, int $currentDepth = 0): ?DataGroupCollection
    {
        // 检查嵌套层级是否超过最大限制
        if ($currentDepth > $maxDepth) {
            throw new \RuntimeException("Maximum nesting level of $maxDepth exceeded while parsing $className.");
        }

        $cachedCollection = GlobalDataCollectionCache::get($className, $groupName);
        if ($cachedCollection) {
            foreach ($cachedCollection->getProperties() as $property) {
                $this->assembleChildren(
                    dataCollection: $property,
                    groupName: $groupName,
                    maxDepth: $maxDepth,
                    currentDepth: $currentDepth
                );
            }
            return $cachedCollection;
        }

        $globalDataCollection = new DataGroupCollection(groupName: $groupName, className: $className);
        $reflectionClass      = ReflectionClassInstanceManager::get($className);

        foreach ($reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $dataCollection = new DataCollection(
                name: $property->getName(),
                nullable: $property->getType()->allowsNull(),
                defaultValue: $property->getDefaultValue(),
            );

            $typeCollections = SerializeContainer::get()->typeCollectionManager()->getCollectionTo($property);
            $dataCollection->setType(...$typeCollections);

            $this->assembleChildren(
                dataCollection: $dataCollection,
                groupName: $groupName,
                maxDepth: $maxDepth,
                currentDepth: $currentDepth
            );

            $globalDataCollection->put($dataCollection);
        }

        // 将解析结果存入缓存
        GlobalDataCollectionCache::put($className, $groupName, $globalDataCollection);

        return $globalDataCollection;
    }

    /**
     * 组装 Children 信息
     *
     * @throws ReflectionException
     */
    private function assembleChildren(
        DataCollection $dataCollection,
        string $groupName,
        int $maxDepth,
        int $currentDepth
    ): void {
        foreach ($dataCollection->getType() as $type) {
            if ($type->kind->existsClass()) {
                $childCollection = $this->parseSerializeClass(
                    groupName: $groupName,
                    className: $type->className,
                    maxDepth: $maxDepth,
                    currentDepth: $currentDepth + 1
                );
                $dataCollection->addChildren($childCollection);
            }
        }
    }

    public function setPayload(mixed $payload): void
    {
    }

    public function toArray()
    {
    }
}
