<?php

namespace Astral\Serialize\Support\Collections;

use Astral\Serialize\Enums\TypeKindEnum;
use Astral\Serialize\Resolvers\PropertyTypesContextResolver;
use Astral\Serialize\SerializeContainer;
use Astral\Serialize\Tests\TestRequest\Other\ReqOtherEnum;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Object_;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;
use ReflectionException;

class TypeCollectionManager
{
    public function __construct(
        protected readonly PropertyTypesContextResolver $propertyTypesContextResolver,
        protected readonly TypeResolver $typeResolver,
    ) {}

    /**
     * 获取属性的类型集合
     *
     * @param ReflectionProperty $property
     * @return TypeCollection[]
     * @throws ReflectionException
     */
    public function getCollectionTo(ReflectionProperty $property): array
    {
        $type = $property->getType();


        $typeDocBlock = $this->resolveTypeFromDocBlock($property);

        // 判断是联合类型 但是存在 $varDoc 取 $varDoc
        if ($typeDocBlock && ($type instanceof  ReflectionUnionType || in_array($type->getName(), ['array', 'object'])))
            return $this->processDocCommentNamedType($typeDocBlock);
        // 判断是否是联合类型
        if ($type instanceof ReflectionUnionType) {
            return $this->processUnionType($type, $property);
        }
        // 判断是否是单一类型
        else if ($type instanceof ReflectionNamedType) {
            return [$this->processNamedType($type, $property)];
        }

        // 如果没有类型，抛出异常
        throw new ReflectionException(sprintf(
            'Property "%s" in class "%s" does not have a valid type.',
            $property->getName(),
            $property->getDeclaringClass()->getName()
        ));
    }

    /**
     * 处理联合类型 (string|int|null 等)
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
     * 处理单一类型
     *
     * @param ReflectionNamedType $type
     * @return TypeCollection|array
     */
    public function processNamedType(ReflectionNamedType $type, ReflectionProperty $property): TypeCollection|array
    {

        // 获取类型名称
        $typeName = $type->getName();
        $className = class_exists($typeName) ?  $typeName : null;

        // 尝试获取className
        // if (!$type->isBuiltin() && !class_exists($typeName)) {
        //     $context = $this->propertyTypesContextResolver->execute($property->getDeclaringClass());;
        //     $className = SerializeContainer::get()->typeResolver()->resolve($typeName, $context)->__toString;
        // }

        return new TypeCollection(
            kind: TypeKindEnum::getNameTo($typeName, $className),
            className: $className
        );
    }

    protected function processDocCommentNamedType(?Type $typesDocBlock): array
    {
        $collections = [];
        foreach ($typesDocBlock as $type) {
            ['typeName' => $typeName, 'className' => $className] = SerializeContainer::get()->propertyTypeDocResolver()->resolve($type);
            $collections[] = new TypeCollection(
                kind: TypeKindEnum::getNameTo($typeName, $className),
                className: $className
            );
        }

        return $collections;
    }


    protected function resolveTypeFromDocBlock(ReflectionProperty $property): ?Type
    {
        // 获取 DocBlock 注释
        $docComment = $property->getDocComment();
        if (!$docComment) {
            return null;
        }

        $context = $this->propertyTypesContextResolver->execute($property->getDeclaringClass());
        $docBlock = SerializeContainer::get()->docBlockFactory()->create($docComment, $context);

        $varTags = $docBlock->getTagsByName('var');
        if (empty($varTags)) {
            return null;
        }

        /** @var Var_ $varTag */
        $varTag = $varTags[0];

        // 获取类型并返回
        return $varTag->getType() ?? null;
    }
}
