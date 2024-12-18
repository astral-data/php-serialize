<?php

namespace Astral\Serialize\Enums;

enum TypeKindEnum
{
    case STRING;
    case INT;
    case FLOAT;
    case BOOLEAN;
    case ARRAY;
    case OBJECT;
    case CLASS_OBJECT;
    case COLLECT_OBJECT;
    case ENUM;
    case DATE;

    public function existsClass(): bool
    {
        return $this === self::CLASS_OBJECT || $this === self::COLLECT_OBJECT;
    }

    public function isPrimitive(): bool
    {
        return in_array($this, [self::STRING, self::INT, self::FLOAT, self::BOOLEAN, self::ARRAY], true);
    }

    public static function getNameTo(string $type, ?string $className = null): self
    {

        if ($className && enum_exists($className)) {
            return self::ENUM;
        } elseif ($className && $type == 'array' && class_exists($className)) {
            return self::COLLECT_OBJECT;
        } elseif ($className && class_exists($className) && $type != 'array') {
            return self::CLASS_OBJECT;
        }

        return match ($type) {
            'string' => self::STRING,
            'int' ,'integer'   => self::INT,
            'double', 'float' => self::FLOAT,
            'bool'   => self::BOOLEAN,
            'array'  => self::ARRAY,
            'object' => self::OBJECT,
        };
    }
}
