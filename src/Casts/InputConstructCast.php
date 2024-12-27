<?php

declare(strict_types=1);

namespace Astral\Serialize\Casts;

use ReflectionClass;
use ReflectionException;
use Astral\Serialize\Support\Collections\ConstructDataCollection;

class InputConstructCast
{
    /**
     * @param array<string,ConstructDataCollection> $constructorParameters
     * @param object $object
     * @param array|null $readonlyVols
     * @return void
     */
    public function resolve(array $constructorParameters, object $object, ?array $readonlyVols): void
    {
        $args = [];
        foreach ($constructorParameters as $param) {
            $args[] = $readonlyVols[$param->name] ?? $object->{$param->name};
        }

        $object->__construct(...$args);
    }


}
