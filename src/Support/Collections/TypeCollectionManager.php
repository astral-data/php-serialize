<?php

namespace Astral\Serialize\Support\Collections;

use Astral\Serialize\Enums\TypeKindEnum;
use Astral\Serialize\Resolvers\PropertyTypesContextResolver;
use Astral\Serialize\SerializeContainer;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\TypeResolver;
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
    ) {
    }

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
        if( $typeDocBlock && ( $type instanceof  ReflectionUnionType || in_array($type->getName(),['array','object'])))
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
    protected function processUnionType(ReflectionUnionType $type, ReflectionProperty $property): array
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
    protected function processNamedType(ReflectionNamedType $type): TypeCollection|array
    {
        // 获取类型名称
        $typeName = $type->getName();
        $className = $typeName;

        // 尝试获取className
        if($type->isBuiltin() && !class_exists($typeName)){

        }

        return new TypeCollection(
            kind: TypeKindEnum::getNameTo($typeName,$className),
            className: null,
            nullable: $type->allowsNull()
        );
    }

    protected function processDocCommentNamedType(?Type $typesDocBlock): array
    {
        foreach ($typesDocBlock as $type){

        }

        $type = $varTag->getType();
        if ($type instanceof Object_) {
            // 返回完整的类名
            return $type->getFqsen()->__toString();
        }

        return null; // 无法解析为类名
    }


    protected function resolveTypeFromDocBlock(ReflectionProperty $property): ?\phpDocumentor\Reflection\Type
    {
        // 获取 DocBlock 注释
        $docComment = $property->getDocComment();
        if (!$docComment) {
            return null; // 如果没有 DocBlock 注释，直接返回 null
        }

        $context = $this->propertyTypesContextResolver->execute($property->getDeclaringClass());
        $docBlock = SerializeContainer::get()->docBlockFactory()->create($docComment, $context);

        $varTags = $docBlock->getTagsByName('var');
        if (empty($varTags)) {
            return null; // 如果没有 @var 标签，直接返回 null
        }

        /** @var Var_ $varTag */
        $varTag = $varTags[0];

        // 获取类型并返回
        return $varTag->getType() ?? null;
    }



}
