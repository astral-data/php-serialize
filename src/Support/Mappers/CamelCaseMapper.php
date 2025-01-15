<?php

declare(strict_types=1);

namespace Astral\Serialize\Support\Mappers;

use Attribute;
use ReflectionProperty;
use Illuminate\Support\Str;
use Astral\Serialize\Contracts\Mappers\NameMapper;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Contracts\Attribute\DataCollectionCastInterface;

class CamelCaseMapper implements NameMapper
{
    public function resolve(string $name): string
    {
        return  Str::camel($name);
    }
}
