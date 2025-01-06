<?php

namespace Astral\Serialize\Resolvers;

use Astral\Serialize\Casts\InputConstructCast;
use Astral\Serialize\Exceptions\NotFoundAttributePropertyResolver;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Collections\GroupDataCollection;
use Astral\Serialize\Support\Context\ChoosePropertyContext;
use Astral\Serialize\Support\Context\ChooseSerializeContext;
use Astral\Serialize\Support\Context\InputValueContext;
use Astral\Serialize\Support\Instance\ReflectionClassInstanceManager;
use ReflectionException;

/**
 * @template T
 */
class PropertyInputValueResolver
{
    public function __construct(
        protected readonly ReflectionClassInstanceManager $reflectionClassInstanceManager,
        private readonly InputValueCastResolver $inputValueCastResolver,
        protected readonly InputConstructCast $inputConstructCast,
        protected readonly GroupResolver $groupResolver,
    ) {

    }

    /**
     * @throws NotFoundAttributePropertyResolver
     * @throws ReflectionException
     * @return T
     */
    public function resolve(ChooseSerializeContext $chooseContext, GroupDataCollection $groupCollection, array $payload)
    {

        /** @var T $reflectionClass */
        $reflectionClass =  $this->reflectionClassInstanceManager->get($groupCollection->getClassName());
        $object          = $reflectionClass->newInstanceWithoutConstructor();

        $context         = new InputValueContext(
            className: $groupCollection->getClassName(),
            classInstance: $object,
            payload: $payload,
            propertyInputValueResolver: $this,
            chooseSerializeContext: $chooseContext,
        );

        $properties = $groupCollection->getProperties();

        $constructInputs =  $this->inputConstructCast->getNotPromoted($groupCollection->getConstructProperties(), $payload);

        foreach ($properties as $collection) {

            $name = $collection->getName();

            $chooseContext->addProperty(new ChoosePropertyContext($name, $chooseContext));
            $matchInput = $this->matchInputNameAndValue($chooseContext, $collection, $groupCollection, $payload);

            if ($matchInput === false) {
                continue;
            }

            $chooseContext->getProperty($name)->setInputName($matchInput['name']);

            $resolvedValue = $matchInput['value'];

            $resolvedValue = $this->inputValueCastResolver->resolve(
                value:$resolvedValue,
                collection:$collection,
                context: $context,
            );

            if ($groupCollection->hasConstruct() && $groupCollection->hasConstructProperty($name)) {
                $constructInputs[$name] = $resolvedValue;
            } else {
                $collection->getProperty()->setValue($object, $resolvedValue);
            }
        }

        if ($groupCollection->hasConstruct()) {
            $this->inputConstructCast->resolve($groupCollection->getConstructProperties(), $object, $constructInputs);
        }

        return $object;

    }

    public function matchInputNameAndValue(ChooseSerializeContext $chooseContext, DataCollection $collection, GroupDataCollection $groupCollection, array $payloadKeys): array|false
    {

        $defaultGroup = $chooseContext->serializeClass;
        $groups       = $chooseContext->getGroups();
        $inputNames   = $collection->getInputNamesByGroups($groups, $defaultGroup);

        return !$this->groupResolver->resolveExistsGroupsByDataCollection($collection, $groups, $defaultGroup) || $collection->isInputIgnoreByGroups($groups)
            ? $this->getConstructPropertyValue($groupCollection, $collection, null)
            : $this->findMatch($inputNames ?: [$collection->getName()], $payloadKeys)
            ?? $this->getConstructPropertyValue($groupCollection, $collection, $collection->getDefaultValue());
    }

    private function findMatch(array $inputNames, array $payloadKeys): ?array
    {

        foreach ($inputNames as $name) {
            if (array_key_exists($name, $payloadKeys)) {
                return ['name' => $name,'value' => $payloadKeys[$name]];
            }

            if (str_contains($name, '.')) {
                if (($nestedValue = $this->matchNestedKey($name, $payloadKeys)) !== false) {
                    return ['name' => $name,'value' => $nestedValue];
                }
            }
        }

        return null;
    }

    private function getConstructPropertyValue(GroupDataCollection $groupCollection, DataCollection $collection, mixed $defaultValue): array|false
    {
        $constructProperty = $groupCollection->getConstructProperty($collection->getName());
        return $constructProperty
            ? ['name' => $collection->getName(), 'value' => $constructProperty->defaultValue ?? $defaultValue]
            : false;
    }

    /**
     *
     * @param string $name
     * @param array $payloadKeys
     * @return mixed
     */
    private function matchNestedKey(string $name, array $payloadKeys): mixed
    {
        $keys    = explode('.', $name);
        $current = $payloadKeys;

        foreach ($keys as $key) {

            if (!is_array($current) || !array_key_exists($key, $current)) {
                return false;
            }

            $current = $current[$key];
        }

        return $current;
    }

    private function normalizePayload(array|object $payload): array
    {
        return is_object($payload) ? (array)$payload : $payload;
    }
}
