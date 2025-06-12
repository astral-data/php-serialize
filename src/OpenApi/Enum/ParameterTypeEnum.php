<?php

namespace Astral\Serialize\OpenApi\Enum;

use Astral\Serialize\Enums\TypeKindEnum;
use Astral\Serialize\Support\Collections\TypeCollection;

enum ParameterTypeEnum: string
{
    case ARRAY  = 'array';
    case STRING = 'string';
    case OBJECT = 'object';
    case BOOLEAN = 'boolean';
    case INTEGER = 'integer';
    case NUMBER = 'number';
    case ONE_OF = 'oneOf';
    case ANY_OF = 'anyOf';
    case ALL_OF = 'allOf';

    public function isObject(): bool
    {
        return $this === self::OBJECT;
    }

    public function isArray(): bool
    {
        return $this === self::ARRAY;
    }

    public function isOf(): bool
    {
        return $this === self::ONE_OF ||  $this === self::ANY_OF || $this === self::ALL_OF;
    }

    public static function getBaseEnumByTypeKindEnum(TypeCollection $collection): ?ParameterTypeEnum
    {
        return match (true){
            $collection->kind === TypeKindEnum::STRING => self::STRING,
            $collection->kind === TypeKindEnum::INT => self::INTEGER,
            $collection->kind === TypeKindEnum::FLOAT => self::NUMBER,
            $collection->kind === TypeKindEnum::BOOLEAN => self::BOOLEAN,
            default => null,
        };
    }

    /**
     * @param TypeCollection[] $types
     * @param string $className
     * @return ParameterTypeEnum|null
     */

    public static function getArrayAndObjectEnumBy(array $types, string $className): ?ParameterTypeEnum
    {

        foreach ($types as $collection){
            if($className === $collection->className && in_array($collection->kind, [TypeKindEnum::CLASS_OBJECT, TypeKindEnum::OBJECT], true)){
                return self::OBJECT;
            }

            if( $className === $collection->className && in_array($collection->kind, [TypeKindEnum::ARRAY, TypeKindEnum::COLLECT_SINGLE_OBJECT, TypeKindEnum::COLLECT_UNION_OBJECT], true)){
                return self::ARRAY;
            }
        }

        return null;
    }

    /**
     * @param TypeCollection[] $types
     */
    public static function getByTypes(array $types): ParameterTypeEnum
    {

        $count = count($types);

        if($count === 1){
            $type = current($types)->kind;
            return match (true){
                $type === TypeKindEnum::INT => self::INTEGER,
                $type === TypeKindEnum::FLOAT => self::NUMBER,
                $type === TypeKindEnum::BOOLEAN => self::BOOLEAN,
                $type === TypeKindEnum::OBJECT, $type === TypeKindEnum::CLASS_OBJECT => self::OBJECT,
                $type === TypeKindEnum::ARRAY, $type === TypeKindEnum::COLLECT_SINGLE_OBJECT , $type === TypeKindEnum::COLLECT_UNION_OBJECT => self::ARRAY,
                default => self::STRING,
            };
        }

        $hasUnion = false;
        foreach ($types as $type){
            if($type->kind === TypeKindEnum::COLLECT_UNION_OBJECT){
                $hasUnion = true;
            }
        }

        return $hasUnion ? self::ANY_OF : self::ONE_OF;


    }
}
