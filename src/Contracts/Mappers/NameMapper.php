<?php

namespace Astral\Serialize\Contracts\Mappers;

interface NameMapper
{
    public function resolve(string $name): string;
}
