<?php

namespace Astral\Serialize\Resolvers;

use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use ReflectionClass;
use ReflectionProperty;
use phpDocumentor\Reflection\Type;
use Astral\Serialize\SerializeContainer;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\ContextFactory;

class PropertyTypesContextResolver
{
    /** @var array<string, Context> */
    protected static array $contexts = [];


    /**
     * @return Type[]|null
     */
    public function resolveTypeFromDocBlock(ReflectionProperty $property): array|null
    {
        // 获取 DocBlock 注释
        $docComment = $property->getDocComment();
        if (!$docComment) {
            return null;
        }

        $context  = $this->resolveContexts($property->getDeclaringClass());
        $docBlock = SerializeContainer::get()->docBlockFactory()->create($docComment, $context);

        $varTags = $docBlock->getTagsByName('var');
        if (empty($varTags)) {
            return null;
        }

        /** @var Var_ $varTag */
        $varTag = $varTags[0];

        $tag = $varTag->getType() ?? null;
        if (!$tag) {
            return null; // 如果没有类型信息，返回 null
        }

        // 如果类型是 Compound，返回其子类型数组
        if ($tag instanceof Compound) {
            $types = [];
            foreach ($tag as $subType) {
                $types[] = $subType;
            }
            return $types;
        }

        // 否则，将单一类型包装成数组返回
        return [$tag];
    }


    public function resolveContexts(ReflectionClass $reflection): Context
    {
        return self::$contexts[$reflection->getName()] ??= (new ContextFactory())->createFromReflector($reflection);
    }
}
