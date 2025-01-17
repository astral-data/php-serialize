<?php

declare(strict_types=1);

namespace Astral\Serialize\Support\Mappers;

use Astral\Serialize\Contracts\Mappers\NameMapper;
use Illuminate\Support\Str;

class DotCaseMapper implements NameMapper
{
    /**
     * dot.case
     **/
    public function resolve(string $name): string
    {
        $snakeCase = preg_replace('/([a-z])([A-Z])/', '$1_$2', $name);
        $snakeCase = str_replace(['-', '.'], '_', $snakeCase);
        $snakeCase = Str::lower($snakeCase);
        return str_replace('_', '.', $snakeCase);
    }
}
