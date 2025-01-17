<?php

declare(strict_types=1);

namespace Astral\Serialize\Support\Mappers;

use Astral\Serialize\Contracts\Mappers\NameMapper;
use Illuminate\Support\Str;

class CamelCaseMapper implements NameMapper
{
    /**
     * camelCase
     **/
    public function resolve(string $name): string
    {
        return Str::of($name)
            ->replaceMatches('/([a-z])([A-Z])/', '$1_$2')
            ->replaceMatches('/[^a-zA-Z0-9]+/', '_')
            ->replaceMatches('/_+/', '_')
            ->trim('_')
            ->lower()
            ->camel()
            ->toString()
        ;
    }
}
