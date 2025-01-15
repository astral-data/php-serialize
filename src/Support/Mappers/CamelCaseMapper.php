<?php

declare(strict_types=1);

namespace Astral\Serialize\Support\Mappers;

use Astral\Serialize\Contracts\Mappers\NameMapper;
use Illuminate\Support\Str;

class CamelCaseMapper implements NameMapper
{
    public function resolve(string $name): string
    {
        return  Str::camel($name);
    }
}
