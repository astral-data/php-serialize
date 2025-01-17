<?php

declare(strict_types=1);

namespace Astral\Serialize\Support\Mappers;

use Astral\Serialize\Contracts\Mappers\NameMapper;
use Illuminate\Support\Str;

class PascalCaseMapper implements NameMapper
{
    /**
     * PascalCase
     **/
    public function resolve(string $name): string
    {
        $cleanedName = preg_replace('/[^a-zA-Z0-9]+/', ' ', $name);
        $cleanedName = preg_replace('/([a-z])([A-Z])/', '$1 $2', $cleanedName);
        return Str::studly(Str::lower($cleanedName));
    }
}
