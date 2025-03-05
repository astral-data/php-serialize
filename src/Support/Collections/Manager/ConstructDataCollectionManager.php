<?php

namespace Astral\Serialize\Support\Collections\Manager;

use Astral\Serialize\Support\Collections\ConstructDataCollection;
use ReflectionMethod;

class ConstructDataCollectionManager
{
    public function getCollectionTo(?ReflectionMethod $method): array
    {
        if ($method === null) {
            return  [];
        }

        $params = $method->getParameters();

        $vols = [];
        foreach ($params as $param) {
            $name = $param->getName();
            $vols[$name] = new ConstructDataCollection(
                name: $name,
                isPromoted: $param->isPromoted(),
                isOptional: $param->isOptional(),
                isNull: $param->allowsNull(),
                defaultValue: $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null,
            );
        }

        return $vols;
    }
}
