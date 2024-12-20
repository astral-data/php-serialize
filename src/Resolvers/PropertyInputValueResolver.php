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
        $context = new InputValueContext($object, $payload);

        $properties = array_filter(
            $groupCollection->getProperties(),
            fn ($property) => !$property->getInputIgnore()
        );

        // 遍历所有属性集合
        foreach ($properties as $collection) {

            $inputName = $this->matchInputName($collection, $payload);
            if ($inputName === false) {
                continue;
            }

            $collection->setChooseInputName($inputName);

            $resolvedValue = &$payload[$inputName];
            $resolvedValue = $this->inputValueCastResolver->resolve(
                value:$resolvedValue,
                collection:$collection,
                context: $context,
            );

            $collection->getProperty()->setValue($object, $resolvedValue);
        }

        return $object;
    }

    public function matchInputName(DataCollection $collection, array $payloadKeys): false|string
    {
        $inputNames = $collection->getInputNames();

        if (!$inputNames && array_key_exists($collection->getName(), $payloadKeys)) {
            return $collection->getName();
        }

        foreach ($inputNames as $name) {
            if (array_key_exists($name, $payloadKeys)) {
                return $name;
            }
        }

        return false;
    }

    private function normalizePayload(array|object $payload): array
    {
        return is_object($payload) ? (array)$payload : $payload;
    }
}
