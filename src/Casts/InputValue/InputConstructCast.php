<?php

declare(strict_types=1);

namespace Astral\Serialize\Casts\InputValue;

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
            $value = array_key_exists($param->name, $readonlyVols) ? $readonlyVols[$param->name] : $object->{$param->name};
            if ($param->isNull === false && $value === null && $param->defaultValue !== null) {
                $value = $param->defaultValue;
            }
            $args[] =  $value;
        }

        $object->__construct(...$args);
    }

    /**
     * @param array<string,ConstructDataCollection> $constructorParameters
     * @param array $payload
     * @return array
     */
    public function getNotPromoted(array $constructorParameters, array $payload): array
    {
        $vols = [];
        foreach ($constructorParameters as $param) {
            if (!$param->isPromoted && isset($payload[$param->name])) {
                $vols[$param->name] = $payload[$param->name];
            }
        }

        return $vols;
    }
}
