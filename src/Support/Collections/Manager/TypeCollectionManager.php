<?php

namespace Astral\Serialize\Support\Collections\Manager;

use Astral\Serialize\Enums\TypeKindEnum;
use Astral\Serialize\Resolvers\PropertyTypeDocResolver;
use Astral\Serialize\Resolvers\PropertyTypesContextResolver;
use Astral\Serialize\Support\Collections\TypeCollection;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\TypeResolver;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionType;
use ReflectionUnionType;

class TypeCollectionManager
{
    public function __construct(
        protected readonly PropertyTypeDocResolver $propertyTypeDocResolver,
        protected readonly PropertyTypesContextResolver $propertyTypesContextResolver,
        protected readonly TypeResolver $typeResolver
    ) {
    }

    /**
     *
     * @param ReflectionProperty $property
     * @return TypeCollection[]
     */
    public function getCollectionTo(ReflectionProperty $property): array
    {
        $type = $property->getType();

        $typeDocBlock = $this->propertyTypesContextResolver->resolveTypeFromDocBlock($property);
        if ($typeDocBlock && ($type instanceof ReflectionUnionType || ($type instanceof ReflectionNamedType && in_array($type->getName(), ['array', 'object']))) ) {
            return $this->processDocCommentNamedType($typeDocBlock);
        }

        if ($type instanceof ReflectionUnionType) {
            return $this->processUnionType($type, $property);
        }

        if ($type instanceof ReflectionNamedType) {
            return [$this->processNamedType($type, $property)];
        }

        return [new TypeCollection(TypeKindEnum::MIXED, null)];
    }

    /**
     *
     * @param ReflectionUnionType $type
     * @param ReflectionProperty $property
     * @return TypeCollection[]
     */
    public function processUnionType(ReflectionUnionType $type, ReflectionProperty $property): array
    {
        $collections = [];
        foreach ($type->getTypes() as $singleType) {
            if ($singleType instanceof ReflectionNamedType) {
                $collections[] = $this->processNamedType($singleType, $property);
            }
        }
        return $collections;
    }

    /**
     *
     * @param ReflectionNamedType $type
     * @param ReflectionProperty $property
     * @return TypeCollection|array
     */
    public function processNamedType(ReflectionNamedType $type, ReflectionProperty $property): TypeCollection|array
    {
        // 获取类型名称
        $typeName  = $type->getName();
        $className = class_exists($typeName) ? $typeName : null;

        // 尝试获取className
        // if (!$type->isBuiltin() && !class_exists($typeName)) {
        //     $context = $this->propertyTypesContextResolver->execute($property->getDeclaringClass());;
        //     $className = SerializeContainer::get()->typeResolver()->resolve($typeName, $context)->__toString;
        // }

        // var_dump($typeName, $className);

        return new TypeCollection(
            kind: TypeKindEnum::getNameTo($typeName, $className),
            className: $className
        );
    }

    /**
     * @param Type[] $typesDocBlock
     * @return array
     */
    protected function processDocCommentNamedType(array $typesDocBlock): array
    {
        $collections = [];
        foreach ($typesDocBlock as $type) {
            ['typeName' => $typeName, 'classNames' => $classNames] = $this->propertyTypeDocResolver->resolve($type);

            foreach ($classNames as $className) {
                $collections[]  = new TypeCollection(
                    kind: TypeKindEnum::getNameTo($typeName, $className),
                    className: $className
                );
            }
        }

        return $collections;
    }
}
