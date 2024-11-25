<?php

namespace Astral\Serialize\Resolvers;

use phpDocumentor\Reflection\Type;
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
        $className = $valueType instanceof Object_
            ? $valueType->getFqsen()->__toString()
            : (string)$valueType;

        return [
            'typeName'  => 'array',
            'className' => ltrim($className, '\\'),
        ];
    }

    protected function resolveObjectType(Object_ $type): array
    {
        return [
            'typeName'  => 'object',
            'className' => ltrim($type->getFqsen()->__toString(), '\\'),
        ];
    }

    protected function resolveMixedType(Mixed_ $type): array
    {
        return [
            'typeName'  => 'string',
            'className' => null,
        ];
    }

    protected function resolveDefaultType(Type $type): array
    {
        return [
            'typeName'  => (string)$type,
            'className' => null,
        ];
    }
}
