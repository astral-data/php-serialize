<?php

namespace Astral\Serialize\Support\Mappers;
use Illuminate\Support\Str;
use Astral\Serialize\Contracts\Mappers\NameMapper;

class SnakeCaseMapper implements NameMapper
{
    public function resolve(string $name): string
    {
        return  Str::snake($name);
    }
}
