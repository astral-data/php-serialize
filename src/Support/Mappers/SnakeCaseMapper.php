<?php

namespace Astral\Serialize\Support\Mappers;

use Astral\Serialize\Contracts\Mappers\NameMapper;
use Illuminate\Support\Str;

class SnakeCaseMapper implements NameMapper
{
    public function resolve(string $name): string
    {
        return  Str::snake($name);
    }
}
