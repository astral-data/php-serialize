<?php

namespace Astral\Serialize\Enums;


use RuntimeException;

enum TypeKindEnum
{
    case MIXED;
    case STRING;
    case INT;
    case FLOAT;
    case BOOLEAN;
    case ARRAY;
    case OBJECT;
    case CLASS_OBJECT;
    case COLLECT_SINGLE_OBJECT;
    case COLLECT_UNION_OBJECT;
    case ENUM;
    case DATE;

    public function existsCollectClass(): bool
    {
        return $this === self::CLASS_OBJECT || $this === self::COLLECT_SINGLE_OBJECT || $this === self::COLLECT_UNION_OBJECT;
    }

    public function existsFakerClass(): bool
    {
        return  $this === self::OBJECT || $this === self::CLASS_OBJECT || $this === self::COLLECT_SINGLE_OBJECT || $this === self::COLLECT_UNION_OBJECT;
    }

    public function isCollect(): bool
    {
        return $this === self::COLLECT_UNION_OBJECT || $this === self::COLLECT_SINGLE_OBJECT;
    }

    public function isPrimitive(): bool
    {
        return in_array($this, [self::STRING, self::INT, self::FLOAT, self::BOOLEAN, self::ARRAY], true);
    }

    public static function getNameTo(string $type, ?string $className = null): self
    {
        if ($className && enum_exists($className)) {
            return self::ENUM;
        }

        if ($className && $type === 'array_union' && class_exists($className)) {
            return self::COLLECT_UNION_OBJECT;
        }

        if ($className && $type === 'array' && class_exists($className)) {
            return self::COLLECT_SINGLE_OBJECT;
        }

        if ($className && class_exists($className) && $type !== 'array') {
            return self::CLASS_OBJECT;
        }

        return match ($type) {
            'string' => self::STRING,
            'int' ,'integer'   => self::INT,
            'double', 'float' => self::FLOAT,
            'bool'   => self::BOOLEAN,
            'array'  => self::ARRAY,
            'object' => self::OBJECT,
            'mixed'  => self::MIXED,
            default  => throw new RuntimeException("not found type $type"),
        };
    }
}
