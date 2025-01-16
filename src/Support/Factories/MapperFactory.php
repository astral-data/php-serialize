<?php

namespace Astral\Serialize\Support\Factories;

use InvalidArgumentException;
use Psr\SimpleCache\CacheInterface;
use Astral\Serialize\Contracts\Mappers\NameMapper;
use Astral\Serialize\Support\Mappers\CamelCaseMapper;
use Astral\Serialize\Support\Mappers\SnakeCaseMapper;

class MapperFactory
{
    /** @var array<class-string,NameMapper> */
    public static array $instance = [];

    public static function build(string $className): NameMapper
    {
        if (!isset(self::$instance[$className])) {
            self::$instance[$className] = match ($className) {
                CamelCaseMapper::class => new CamelCaseMapper(),
                SnakeCaseMapper::class => new SnakeCaseMapper(),
                default => throw new InvalidArgumentException(sprintf('Unsupported mapper class "%s"', $className)),
            };
        }

        return self::$instance[$className];
    }
}
