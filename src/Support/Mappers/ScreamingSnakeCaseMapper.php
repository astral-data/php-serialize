<?php

declare(strict_types=1);

namespace Astral\Serialize\Support\Mappers;

use Astral\Serialize\Contracts\Mappers\NameMapper;
use Illuminate\Support\Str;

class ScreamingSnakeCaseMapper implements NameMapper
{
    /**
     * SCREAMING_SNAKE_CASE
     **/
    public function resolve(string $name): string
    {
        $name = str_replace(['-', '.'], '_', $name);
        $snakeCase = Str::snake($name);
        return Str::upper($snakeCase);
    }
}
