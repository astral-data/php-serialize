<?php

namespace Astral\Serialize\Support\Context;

use Astral\Serialize\Exceptions\NotFoundGroupException;
use Astral\Serialize\Faker\FakerResolver;
use Astral\Serialize\Resolvers\Casts\DataCollectionCastResolver;
use Astral\Serialize\Resolvers\Casts\NormalizerCastResolver;
use Astral\Serialize\Resolvers\GroupResolver;
use Astral\Serialize\Resolvers\InputResolver;
use Astral\Serialize\Resolvers\OutputResolver;
use Astral\Serialize\Serialize;
use Astral\Serialize\SerializeContainer;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Collections\GroupDataCollection;
use Astral\Serialize\Support\Collections\Manager\ConstructDataCollectionManager;
use Astral\Serialize\Support\Instance\ReflectionClassInstanceManager;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use ReflectionProperty;
use RuntimeException;

/**
 * @template T
 */
class SerializeContext
{
    private array $groups = [];
    private array $responses = [];

    public function __construct(
        /** @var class-string<T> */
        private readonly string                         $serializeClassName,
        private readonly ChooseSerializeContext         $chooseSerializeContext,
        private readonly CacheInterface                 $cache,
        private readonly ReflectionClassInstanceManager $reflectionClassInstanceManager,
        private readonly GroupResolver                  $groupResolver,
        private readonly DataCollectionCastResolver     $dataCollectionCastResolver,
        private readonly ConstructDataCollectionManager $constructDataCollectionManager,
        private readonly InputResolver                  $propertyInputValueResolver,
        private readonly OutputResolver                 $propertyToArrayResolver,
        private readonly FakerResolver                  $fakerResolver,
        private readonly NormalizerCastResolver         $normalizerCastResolver,
    ) {

    }

    public function setCode(string|int $code, $description ='' , $field = 'code'): void
    {
        $this->responses[$field] = ['description' => $description,'value' => $code];
    }

    public function setMessage(string $message, $description ='' , $field = 'message'): void
    {
        $this->responses[$field] = ['description' => $description,'value' => $message];
    }

    public function withResponses(array $responses): self
    {
        $this->responses = $responses;
        return $this;
    }

    public function getResponses(): array
    {
        return $this->responses ?? [];
    }

    public function getChooseSerializeContext(): ChooseSerializeContext
    {
        return $this->chooseSerializeContext;
    }

    /**
     * @param array $groups
     * @return static
     * @throws InvalidArgumentException
     * @throws NotFoundGroupException
     */
    public function setGroups(array $groups): static
    {
        $reflectionClass = $this->reflectionClassInstanceManager->get($this->serializeClassName);
        $this->groupResolver->resolveExistsGroupsByClass($reflectionClass, $this->serializeClassName, $groups);
        $this->groups = $groups;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * @throws InvalidArgumentException
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
     * @throws InvalidArgumentException
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

            $dataCollection = new DataCollection(
                groups: $this->groupResolver->getGroupsTo($property),
                parentGroupCollection: $globalDataCollection,
                name: $property->getName(),
                isNullable: $property->getType()?->allowsNull() ?? true,
                isReadonly: $property->isReadOnly(),
                attributes: array_merge($property->getAttributes(), $property->getDeclaringClass()->getAttributes()),
                defaultValue: $property->hasDefaultValue() ? $property->getDefaultValue() : null,
                property: $property,
            );

            $typeCollections = SerializeContainer::get()->typeCollectionManager()->getCollectionTo($property);
            $dataCollection->setTypes(...$typeCollections);
            $this->dataCollectionCastResolver->resolve($dataCollection);

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
     * @throws InvalidArgumentException
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
            if ($type->kind->existsCollectClass()) {
                $childCollection = $this->parseSerializeClass(
                    className: $type->className,
                    maxDepth: $maxDepth,
                    currentDepth: $currentDepth + 1
                );
                $dataCollection->addChildren($childCollection);
            }
        }
    }

    /**
     * @param mixed ...$payload
     */
    public function from(mixed ...$payload): object
    {
        $payloads = [];
        foreach ($payload as $field => $itemPayload) {
            $itemPayload = $this->normalizerCastResolver->resolve($itemPayload);
            $values   = is_numeric($field) && is_array($itemPayload) ? $itemPayload : [$field => $itemPayload];
            $payloads = [...$payloads, ...$values];
        }

        $this->chooseSerializeContext->setGroups($this->getGroups());

        /** @var T $object */
        $object = $this->propertyInputValueResolver->resolve($this->chooseSerializeContext, $this->getGroupCollection(), $payloads);

        if ($object instanceof Serialize && $object->getContext() === null) {
            $object->setContext($this);
        }

        return $object;

    }

    public function faker()
    {
        $this->chooseSerializeContext->setGroups($this->getGroups());
        return $this->fakerResolver->resolve($this->chooseSerializeContext, $this->getGroupCollection());
    }

    /**
     * @throws InvalidArgumentException
     */
    public function toArray(object $object): array
    {
        $this->chooseSerializeContext->setGroups($this->getGroups());
        return $this->propertyToArrayResolver->resolve($this->chooseSerializeContext, $this->getGroupCollection(), $object);
    }

}
