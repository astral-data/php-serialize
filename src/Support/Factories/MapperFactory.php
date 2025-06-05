<?php

namespace Astral\Serialize\Support\Factories;

use Astral\Serialize\Contracts\Mappers\NameMapper;
use InvalidArgumentException;

class MapperFactory
{
    /** @var array<class-string,NameMapper> */
    public static array $instance = [];

    public static function build(string $className): NameMapper
    {
        if (!isset(self::$instance[$className])) {
            self::$instance[$className] = is_subclass_of($className, NameMapper::class) ? new $className() :
                throw new InvalidArgumentException(sprintf(
                    'Class "%s" must implement the NameMapper interface.',
                    $className
                ));
        }

        return self::$instance[$className];
    }
}
