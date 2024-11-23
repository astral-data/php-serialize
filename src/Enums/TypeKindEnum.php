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
    case CollectObject;
    case ENUM;
    case DATE;


    public static function getNameTo(string $type,?string $className = null): self
    {

            if(enum_exists($className)){
                return self::ENUM;
            }
            else if($className && $type == 'array' && class_exists($className)){
                return self::CollectObject;
            }
            else if($className && class_exists($className) && $type != 'array'){
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

