<?php

namespace Astral\Serialize\Resolvers;

use Astral\Serialize\Exceptions\NotFoundAttributePropertyResolver;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Collections\DataGroupCollection;
use Astral\Serialize\Support\Context\InputValueContext;

class PropertyInputValueResolver
{
    public function __construct(
        private readonly InputValueCastResolver $inputValueCastResolver,
    ) {

    }

    /**
     * @throws NotFoundAttributePropertyResolver
     */
    public function resolve(string|object $serialize, DataGroupCollection $groupCollection, array|object $payload): object
    {
        $object  = is_string($serialize) ? new $serialize() : $serialize;
        $payload = $this->normalizePayload($payload);
        $context = new InputValueContext($object, $payload, $this);

        $properties = array_filter(
            $groupCollection->getProperties(),
            fn ($property) => !$property->getInputIgnore()
        );

        // 遍历所有属性集合
        foreach ($properties as $collection) {

            $matchInput = $this->matchInputNameAndValue($collection, $payload);
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

            $collection->getProperty()->setValue($object, $resolvedValue);
        }

        return $object;
    }

    public function matchInputNameAndValue(DataCollection $collection, array $payloadKeys): array|false
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
        $keys = explode('.', $name);
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
