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
        $name = preg_replace('/([a-z])([A-Z])/', '$1_$2', $name);
        $name = preg_replace('/[^a-zA-Z0-9]+/', '_', $name);
        $name = preg_replace('/_+/', '_', $name);
        return Str::camel(Str::lower(trim($name, '_')));
    }
}
