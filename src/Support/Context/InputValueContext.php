<?php

namespace Astral\Serialize\Support\Context;

class InputValueContext
{
    public function __construct(
        public readonly object $currentObject,
        public readonly array $payload,
    ) {

    }
}
