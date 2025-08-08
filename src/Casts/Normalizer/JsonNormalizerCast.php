<?php

namespace Astral\Serialize\Casts\Normalizer;

use Astral\Serialize\Contracts\Normalizer\NormalizerCastInterface;
use JsonException;

class JsonNormalizerCast implements NormalizerCastInterface
{
    public function match(mixed $values): bool
    {
        return is_string($values);
    }

    public function resolve(mixed $values): array
    {
        if($this->match($values)){
            try {
                $decoded = json_decode($values, true, 512, JSON_THROW_ON_ERROR);
                return is_array($decoded) ? $decoded : $values;
            } catch (JsonException $e) {

            }
        }

        return $values;
    }
}