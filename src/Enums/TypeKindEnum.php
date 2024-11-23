<?php

namespace Astral\Serialize\Enums;

use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;

enum TypeKindEnum
{
    case STRING;
    case INT;
    case FLOAT;
    case BOOLEAN;
    case ARRAY;
    case OBJECT;
    case COLLECT_OBJECT;
    case ENUM;
    case DATE;



    public function isObjectType(): bool
    {
        return $this === self::OBJECT || $this === self::COLLECT_OBJECT;
    }

    public function isPrimitive(): bool
    {
        return in_array($this, [self::STRING, self::INT, self::FLOAT, self::BOOLEAN, self::ARRAY], true);
    }

    public static function getNameTo(string $type, ?string $className = null): self
    {

        if ($className && enum_exists($className)) {
            return self::ENUM;
        } else if ($className && $type == 'array' && class_exists($className)) {
            return self::COLLECT_OBJECT;
        } else if ($className && class_exists($className) && $type != 'array') {
            return self::OBJECT;
        }

        return match ($type) {
            'string' => self::STRING,
            'int' => self::INT,
            'float' => self::FLOAT,
            'bool' => self::BOOLEAN,
            'array' => self::ARRAY,
        };
    }
}
