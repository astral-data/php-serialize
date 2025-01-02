<?php

namespace Astral\Serialize\Support\Context;

use Astral\Serialize\Exceptions\NotFoundAttributePropertyResolver;
use Astral\Serialize\Exceptions\NotFoundGroupException;
use Astral\Serialize\Resolvers\DataCollectionCastResolver;
use Astral\Serialize\Resolvers\GroupResolver;
use Astral\Serialize\Resolvers\PropertyInputValueResolver;
use Astral\Serialize\SerializeContainer;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Collections\GroupDataCollection;
use Astral\Serialize\Support\Collections\Manager\ConstructDataCollectionManager;
use Astral\Serialize\Support\Instance\ReflectionClassInstanceManager;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use ReflectionException;
use ReflectionProperty;
use RuntimeException;


class SerializeContext
{
    private array $groups = [];

    public function __construct(
        private readonly string                         $serializeClassName,
        private readonly ChooseSerializeContext         $chooseSerializeContext,
        private readonly CacheInterface                 $cache,
        private readonly ReflectionClassInstanceManager $reflectionClassInstanceManager,
        private readonly GroupResolver                  $groupResolver,
        private readonly DataCollectionCastResolver     $dataCollectionCastResolver,
        private readonly ConstructDataCollectionManager $constructDataCollectionManager,
        private readonly PropertyInputValueResolver     $propertyInputValueResolver,
    ) {

    }

    /**
     * @throws InvalidArgumentException
     * @throws ReflectionException
     * @throws NotFoundGroupException
     */
    public function setGroups(array $groups): static
    {
        $reflectionClass = $this->reflectionClassInstanceManager->get($this->serializeClassName);
        $this->groupResolver->resolveExistsGroupsByClass($reflectionClass, $this->serializeClassName, $groups);
        $this->groups = $groups;

        return $this;
    }

    public function getGroups(): array
    {
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
        $cacheKey = $this->getCacheKey();

        if ($this->cache->has($cacheKey)) {
            /** @var GroupDataCollection */
            return $this->cache->get($cacheKey);
        }

        /** @var GroupDataCollection $cachedCollection */
        $cachedCollection = $this->parseSerializeClass($this->serializeClassName);
        $this->cache->set($cacheKey, $cachedCollection); // 将解析结果缓存

        return $cachedCollection;
    }

    private function getCacheKey(): string
    {
        return 'SerializeContext:' . $this->serializeClassName . ':' . implode('|', $this->getGroups());
    }

    /**
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws NotFoundGroupException
     * @throws NotFoundAttributePropertyResolver
     */
    public function parseSerializeClass(string $className, int $maxDepth = 10, int $currentDepth = 0): ?GroupDataCollection
    {
        // max depth
        if ($currentDepth > $maxDepth) {
            throw new RuntimeException("Maximum nesting level of $maxDepth exceeded while parsing $className.");
        }

        $cacheKey         = 'SerializeContextGroupClass:' . $className;
        $cachedCollection = $this->cache->get($cacheKey);
        if ($cachedCollection) {
            foreach ($cachedCollection->getProperties() as $property) {
                $this->assembleChildren(
                    dataCollection: $property,
                    maxDepth: $maxDepth,
                    currentDepth: $currentDepth
                );
            }
            return $cachedCollection;
        }

        $reflectionClass      = $this->reflectionClassInstanceManager->get($className);

        $globalDataCollection = new GroupDataCollection(
            defaultGroups: $this->groupResolver->getDefaultGroups($reflectionClass),
            className: $className,
            constructProperties: $this->constructDataCollectionManager->getCollectionTo($reflectionClass->getConstructor())
        );

        foreach ($reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {


            //            if (!$this->groupResolver->resolveExistsGroupsByProperty($property, $this->serializeClassName, $this->getGroups())) {
            //                continue;
            //            }

            $dataCollection = new DataCollection(
                groups: $this->groupResolver->getGroupsTo($property),
                parentGroupCollection: $globalDataCollection,
                name: $property->getName(),
                isNullable: $property->getType()?->allowsNull() ?? true,
                isReadonly: $property->isReadOnly(),
                attributes: array_merge($property->getDeclaringClass()->getAttributes(), $property->getAttributes()),
                defaultValue: $property->hasDefaultValue() ? $property->getDefaultValue() : null,
                property: $property,
            );

            $typeCollections = SerializeContainer::get()->typeCollectionManager()->getCollectionTo($property);
            $dataCollection->setTypes(...$typeCollections);
            $this->dataCollectionCastResolver->resolve($dataCollection, $property);

            $this->assembleChildren(
                dataCollection: $dataCollection,
                maxDepth: $maxDepth,
                currentDepth: $currentDepth
            );

            $globalDataCollection->put($dataCollection);
        }

        // cache
        $this->cache->set($cacheKey, $globalDataCollection);

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
        int $maxDepth,
        int $currentDepth
    ): void {

        if ($dataCollection->isInputIgnoreByGroups($this->getGroups())) {
            return;
        }

        foreach ($dataCollection->getTypes() as $type) {
            if ($type->kind->existsClass()) {
                $childCollection = $this->parseSerializeClass(
                    //                    groupName: $groupName,
                    className: $type->className,
                    maxDepth: $maxDepth,
                    currentDepth: $currentDepth + 1
                );
                $dataCollection->addChildren($childCollection);
            }
        }
    }

    /**
     * @return object => $this->serializeClassName
     * @throws NotFoundAttributePropertyResolver
     * @throws ReflectionException
     * @throws NotFoundGroupException
     * @throws InvalidArgumentException
     */
    public function from(... $payload): object
    {
        $payloads = [];
        foreach ($payload as $field => $itemPayload) {
            $values   = is_numeric($field) && is_array($itemPayload) ? $itemPayload : [$field => $itemPayload];
            $payloads = array_merge($payloads, $values);
        }

        $this->chooseSerializeContext->setGroups($this->getGroups());
        return $this->propertyInputValueResolver->resolve($this->chooseSerializeContext, $this->getGroupCollection(), $payloads);
    }

    public function toArray()
    {
    }
}
