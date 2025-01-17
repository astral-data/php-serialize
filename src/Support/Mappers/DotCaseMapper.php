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
        return Str::of($name)
            ->replaceMatches('/([a-z])([A-Z])/', '$1_$2')
            ->replace(['-', '.'], '_')
            ->lower()
            ->replace('_', '.')
            ->toString();
    }
}
