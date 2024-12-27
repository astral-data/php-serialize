<?php

namespace Astral\Serialize\Support\Context;

use Astral\Serialize\Support\Collections\ConstructDataCollection;
use Astral\Serialize\Exceptions\NotFoundAttributePropertyResolver;
use Astral\Serialize\Exceptions\NotFoundGroupException;
use Astral\Serialize\Resolvers\DataCollectionCastResolver;
use Astral\Serialize\Resolvers\GroupResolver;
use Astral\Serialize\Resolvers\PropertyInputValueResolver;
use Astral\Serialize\SerializeContainer;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Collections\GroupDataCollection;
use Astral\Serialize\Support\Instance\ReflectionClassInstanceManager;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use ReflectionException;
use ReflectionProperty;
use RuntimeException;
use Astral\Serialize\Support\Collections\Manager\ConstructDataCollectionManager;

class SerializeContext
{
    private array $groups = [];

    public function __construct(
        private readonly string                         $serializeClassName,
        private readonly CacheInterface                 $cache,
        private readonly ReflectionClassInstanceManager $reflectionClassInstanceManager,
        private readonly GroupResolver                  $classGroupResolver,
        private readonly DataCollectionCastResolver     $dataCollectionCastResolver,
        private readonly ConstructDataCollectionManager $constructDataCollectionManager,
        private readonly PropertyInputValueResolver     $propertyInputValueResolver,

    ) {
    }

    /**
     * @throws NotFoundGroupException
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    public function setGroups(array $groups): static
    {
        $reflectionClass = $this->reflectionClassInstanceManager->get($this->serializeClassName);
        $this->classGroupResolver->resolveExistsGroups($reflectionClass, $this->serializeClassName, $groups);
        $this->groups = $groups;

        return $this;
    }

    public function getGroups(): array
    {
        $this->groups = $this->groups ?: [$this->serializeClassName];
        return $this->groups;
    }

    public function getSerializeClassName(): string
    {
        return $this->serializeClassName;
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws NotFoundGroupException
     * @throws NotFoundAttributePropertyResolver
     */
    public function getCollection(): GroupDataCollection
    {
        if ($this->cache->has($this->serializeClassName)) {
            return $this->cache->get($this->serializeClassName);
        }

        return $this->getGroupCollection();
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws NotFoundGroupException
     * @throws NotFoundAttributePropertyResolver
     */
    public function getGroupCollection(): GroupDataCollection
    {
        $cachedCollection = null;
        foreach ($this->getGroups() as $group) {
            if ($this->cache->has($this->serializeClassName . ':' . $group)) {
                /** @var GroupDataCollection $cachedCollection */
                $cachedCollection = $this->cache->get($this->serializeClassName . ':' . $group);
            } else {
                /** @var GroupDataCollection $cachedCollection */
                $cachedCollection = $this->parseSerializeClass($group, $this->serializeClassName);
                $this->cache->set($this->serializeClassName . ':' . $group, $cachedCollection); // 将解析结果缓存
            }
        }

        return $cachedCollection;
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws NotFoundGroupException
     * @throws NotFoundAttributePropertyResolver
     */
    public function parseSerializeClass(string $groupName, string $className, int $maxDepth = 10, int $currentDepth = 0): ?GroupDataCollection
    {
        // max depth
        if ($currentDepth > $maxDepth) {
            throw new RuntimeException("Maximum nesting level of $maxDepth exceeded while parsing $className.");
        }

        $cachedCollection = $this->cache->get($className . ':' . $groupName);
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



        $reflectionClass      = $this->reflectionClassInstanceManager->get($className);

        $globalDataCollection = new GroupDataCollection(
            groupName: $groupName,
            className: $className,
            constructProperties: $this->constructDataCollectionManager->getCollectionTo($reflectionClass->getConstructor())
        );

        foreach ($reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {

            if (!$this->classGroupResolver->resolveExistsGroups($property, $this->serializeClassName, $groupName)) {
                continue;
            }

            $dataCollection = new DataCollection(
                parentGroupCollection: $globalDataCollection,
                name: $property->getName(),
                isNullable: $property->getType()?->allowsNull() ?? true,
                isReadonly: $property->isReadOnly(),
                attributes: array_merge($property->getDeclaringClass()->getAttributes(), $property->getAttributes()),
                property: $property,
                defaultValue: $property->getDefaultValue(),
            );

            $typeCollections = SerializeContainer::get()->typeCollectionManager()->getCollectionTo($property);
            $dataCollection->setTypes(...$typeCollections);
            $this->dataCollectionCastResolver->resolve($dataCollection, $property);

            $this->assembleChildren(
                dataCollection: $dataCollection,
                groupName: $groupName,
                maxDepth: $maxDepth,
                currentDepth: $currentDepth
            );

            $globalDataCollection->put($dataCollection);
        }

        // cache
        $this->cache->set($className . ':' . $groupName, $globalDataCollection);

        return $globalDataCollection;
    }

    /**
     * Children
     *
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws NotFoundGroupException
     * @throws NotFoundAttributePropertyResolver
     */
    private function assembleChildren(
        DataCollection $dataCollection,
        string $groupName,
        int $maxDepth,
        int $currentDepth
    ): void {

        if ($dataCollection->getInputIgnore()) {
            return;
        }

        foreach ($dataCollection->getTypes() as $type) {
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

    /**
     * @throws NotFoundAttributePropertyResolver
     * @throws ReflectionException
     * @throws NotFoundGroupException
     * @throws InvalidArgumentException
     */
    public function from(... $payload): object
    {
        $payloads = [];
        foreach ($payload as $field => $itemPayload) {
            $values   = is_numeric($field) && is_array($itemPayload)  || is_object($itemPayload) ? $itemPayload : [$field => $itemPayload];
            $payloads = array_merge($payloads, $values);
        }

        return $this->propertyInputValueResolver->resolve($this->getGroupCollection(), $payloads);
    }

    public function toArray()
    {
    }
}
