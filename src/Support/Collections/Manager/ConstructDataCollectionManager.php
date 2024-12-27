<?php

namespace Astral\Serialize\Support\Collections\Manager;

use ReflectionMethod;
use Astral\Serialize\Support\Collections\ConstructDataCollection;

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
            $vols[$param->getName()] = new ConstructDataCollection(
                name:$param->getName(),
                isPromoted: $param->isPromoted(),
                isOptional: $param->isOptional()
            );
        }

        return $vols;
    }
}
