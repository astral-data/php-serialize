<?php

namespace Astral\Serialize\Resolvers;

use Astral\Serialize\Casts\InputConstructCast;
use Astral\Serialize\Exceptions\NotFoundAttributePropertyResolver;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Collections\GroupDataCollection;
use Astral\Serialize\Support\Context\InputValueContext;
use Astral\Serialize\Support\Instance\ReflectionClassInstanceManager;
use ReflectionException;

class PropertyInputValueResolver
{
    public function __construct(
        protected readonly ReflectionClassInstanceManager $reflectionClassInstanceManager,
        private readonly InputValueCastResolver $inputValueCastResolver,
        protected readonly InputConstructCast $inputConstructCast,
    ) {

    }

    /**
     * @throws NotFoundAttributePropertyResolver
     * @throws ReflectionException
     */
    public function resolve(GroupDataCollection $groupCollection, array $payload): object
    {
        $reflectionClass =  $this->reflectionClassInstanceManager->get($groupCollection->getClassName());
        $object          = $reflectionClass->newInstanceWithoutConstructor();
        $context         = new InputValueContext($groupCollection->getClassName(), $object, $payload, $this);

        // filter InputIgnore
        $properties = array_filter(
            $groupCollection->getProperties(),
            fn ($property) => !$property->getInputIgnore()
        );

        $constructInputs =  $this->inputConstructCast->getNotPromoted($groupCollection->getConstructProperties(), $payload);
        foreach ($properties as $collection) {

            $matchInput = $this->matchInputNameAndValue($collection, $groupCollection, $payload);
            if ($matchInput === false) {
                continue;
            }

            $collection->setChooseInputName($matchInput['name']);
            $resolvedValue = $matchInput['value'];

            $resolvedValue = $this->inputValueCastResolver->resolve(
                value:$resolvedValue,
                collection:$collection,
                context: $context,
            );

            // construct
            if ($groupCollection->hasConstruct() && $groupCollection->hasConstructProperty($collection->getName())) {
                $constructInputs[$collection->getName()] = $resolvedValue;
            } else {
                $collection->getProperty()->setValue($object, $resolvedValue);
            }
        }

        if ($groupCollection->hasConstruct()) {
            $this->inputConstructCast->resolve($groupCollection->getConstructProperties(), $object, $constructInputs);
        }

        return $object;

    }

    public function matchInputNameAndValue(DataCollection $collection, GroupDataCollection $groupCollection, array $payloadKeys): array|false
    {
        $inputNames = $collection->getInputNames();

        if (!$inputNames && array_key_exists($collection->getName(), $payloadKeys)) {
            return ['name' => $collection->getName(),'value' => $payloadKeys[$collection->getName()]];
        }

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

        // construct
        if ($groupCollection->hasConstructProperty($collection->getName())) {
            $value = $groupCollection->getConstructProperty($collection->getName())->defaultValue ?? $collection->getDefaultValue();
            return ['name' => $collection->getName(), 'value' => $value];
        }

        return  false;
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
