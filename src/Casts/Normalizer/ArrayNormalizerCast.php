<?php

namespace Astral\Serialize\Casts\Normalizer;

use Astral\Serialize\Contracts\Normalizer\NormalizerCastInterface;

class ArrayNormalizerCast implements NormalizerCastInterface
{
    public function match(mixed $values): bool
    {
       return is_object($values) && method_exists($values, 'toArray');
    }

    public function resolve(mixed $values): mixed
    {
        if($this->match($values)){
            return $values->toArray();
        }

        return $values;
    }
}