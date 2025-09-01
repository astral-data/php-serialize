<?php

namespace Astral\Serialize\Contracts\Normalizer;

interface NormalizerCastInterface
{
    public function match(mixed $values): bool;

    public function resolve(mixed $values): mixed;
}
