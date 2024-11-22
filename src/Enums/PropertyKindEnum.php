<?php

namespace Astral\Serialize\Enums;

use ReflectionException;
use ReflectionProperty;

enum PropertyKindEnum
{
    case STRING;
    case INT;
    case FLOAT;
    case BOOLEAN;
    case ARRAY;
    case OBJECT;
    case CollectObject;
    case ENUM;
    case DATE;

    public static function fromTypeName(ReflectionProperty $property): self
    {
        $typeName = $property->getType()->getName();
        return match ($typeName) {
            'string' => self::STRING, // 字符串类型
            'int' => self::INT,       // 整数类型
            'float' => self::FLOAT,   // 浮点数类型
            'bool' => self::BOOLEAN,  // 布尔类型
            'array' => self::ARRAY,   // 数组类型
            'DateTime', 'DateTimeImmutable' => self::DATE, // 日期类型
            default => class_exists($typeName)
                ? (enum_exists($typeName)
                    ? self::ENUM         // 枚举类型
                    : self::OBJECT)      // 对象类型
                : throw new ReflectionException('无法识别的属性类型：' . $typeName)
        };
    }
}
