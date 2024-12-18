<?php

namespace Astral\Serialize\Resolvers;

use Astral\Serialize\Enums\TypeKindEnum;
use Astral\Serialize\Support\Collections\TypeCollection;
use Astral\Serialize\Exceptions\NotFoundAttributePropertyResolver;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Collections\DataGroupCollection;
use Astral\Serialize\Support\Config\ConfigManager;
use Astral\Serialize\Contracts\Resolve\Strategies\ResolveStrategyInterface;

class PropertyInputValueResolver
{
    public function __construct(
        private readonly ConfigManager $configManager,
        private readonly InputValueCastResolver $inputValueCastResolver,
    ) {

    }

    /**
     * @throws NotFoundAttributePropertyResolver
     */
    public function resolve(string|object $serialize, DataGroupCollection $groupCollection, array|object $payload): object
    {

        $object  = is_string($serialize) ? new $serialize() : $serialize;
        $payload = is_object($payload) ? (array)$payload : $payload;

        // 遍历所有属性集合
        foreach ($groupCollection->getProperties() as $collection) {

            if ($collection->getInputIgnore()) {
                continue;
            }

            $inputName = $this->matchInputName($collection, $payload);
            if ($inputName === false) {
                continue;
            }
            $collection->setChooseInputName($inputName);
            $resolvedValue = &$payload[$inputName];

            foreach ($this->configManager->getInputValueCasts() as $cast) {
                if ($cast->match($resolvedValue, $collection)) {
                    $resolvedValue = $cast->resolve($resolvedValue, $collection);
                }
            }

            $this->inputValueCastResolver->resolve($resolvedValue, $collection);
            $object->{$collection->getName()} = $resolvedValue;
        }

        return $object;
    }

    public function matchInputName(DataCollection $collection, array $payloadKeys): false|string
    {
        $inputNames = $collection->getInputNames();

        if (!$inputNames && isset($payloadKeys[$collection->getName()])) {
            return $collection->getName();
        }

        if (count($inputNames) === 1 && isset($payloadKeys[current($inputNames)])) {
            return current($inputNames);
        }

        foreach ($inputNames as $name) {
            if (isset($payloadKeys[$name])) {
                return $name;
            }
        }

        return false;
    }
}
