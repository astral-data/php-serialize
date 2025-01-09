<?php

namespace Astral\Serialize\Resolvers;

use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\AggregatedType;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Mixed_;
use phpDocumentor\Reflection\Types\Object_;

class PropertyTypeDocResolver
{
    public function resolve(Type $type): array
    {
        return match (true) {
            $type instanceof Array_  => $this->resolveArrayType($type),
            $type instanceof Object_ => $this->resolveObjectType($type),
            $type instanceof Mixed_  => $this->resolveMixedType($type),
            default                  => $this->resolveDefaultType($type),
        };
    }

    protected function resolveArrayType(Array_ $type): array
    {
        $valueType = $type->getValueType();

        if ($valueType instanceof  Object_) {
            $className =   ltrim($valueType->getFqsen()->__toString(), '\\');
        } elseif ($valueType instanceof AggregatedType) {
            $className = [];
            foreach ($valueType as $type) {
                $className[] = ltrim($type instanceof Object_
                    ? $type->getFqsen()->__toString()
                    : (string)$valueType, '\\');
            }
        } else {
            $className =  ltrim((string)$valueType, '\\');
        }

        return [
            'typeName'   => is_array($className) ? 'array_union' : 'array',
            'classNames' => is_array($className) ? $className : [$className],
        ];

    }

    protected function resolveObjectType(Object_ $type): array
    {
        return [
            'typeName'   => 'object',
            'classNames' => [ltrim($type->getFqsen()->__toString(), '\\')],
        ];
    }

    protected function resolveMixedType(Mixed_ $type): array
    {
        return [
            'typeName'   => 'string',
            'classNames' => [null],
        ];
    }

    protected function resolveDefaultType(Type $type): array
    {
        return [
            'typeName'   => (string)$type,
            'classNames' => [null],
        ];
    }
}
