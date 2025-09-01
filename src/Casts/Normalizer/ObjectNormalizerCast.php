<?php

namespace Astral\Serialize\Casts\Normalizer;

use Astral\Serialize\Contracts\Normalizer\NormalizerCastInterface;

class ObjectNormalizerCast implements NormalizerCastInterface
{
    public function match(mixed $values): bool
    {
        return is_object($values);
    }

    public function resolve(mixed $values): mixed
    {
        if ($this->match($values)) {
            return (array)$values;
        }

        return $values;
    }
}
