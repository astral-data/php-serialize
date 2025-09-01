<?php

namespace Astral\Serialize\Casts\Normalizer;

use Astral\Serialize\Contracts\Normalizer\NormalizerCastInterface;
use DateTimeInterface;
use Throwable;

class DateTimeNormalizerCast implements NormalizerCastInterface
{
    public function match(mixed $values): bool
    {
        return is_object($values) && is_subclass_of($values, DateTimeInterface::class);
    }

    public function resolve(mixed $values): mixed
    {
        if ($this->match($values)) {
            try {
                return $values->format('Y-m-d H:i:s');
            } catch (Throwable $e) {
            }
        }

        return $values;
    }
}
