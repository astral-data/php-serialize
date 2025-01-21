<?php

namespace Astral\Serialize\Faker\Casts;

use Astral\Serialize\Support\Context\FakerValueContext;
use Astral\Serialize\Support\Collections\TypeCollection;
use Astral\Serialize\Contracts\Attribute\OutValueCastInterface;
use Astral\Serialize\Enums\TypeKindEnum;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Collections\GroupDataCollection;
use Astral\Serialize\Support\Context\ChooseSerializeContext;
use Astral\Serialize\Support\Context\OutContext;

class FakerArrayChildCast implements OutValueCastInterface
{
    public function match(DataCollection $collection, FakerValueContext $context): bool
    {
        return $this->hasCollectObjectType($collection);
    }

    /**
     * Resolve the input value for the given collection and context.
     */
    public function resolve(DataCollection $collection, FakerValueContext $context): mixed
    {
        $type     = $this->findCollectObjectType($collection);
        $childCollection = $collection->getChildren()[$type->className];


        if ($type->kind === TypeKindEnum::COLLECT_SINGLE_OBJECT || $type->kind === TypeKindEnum::COLLECT_UNION_OBJECT) {
            return $this->resolveArray($childCollection, $context);
        }

        return $this->resolveSingle($childCollection, $context);
    }

    /**
     */
    private function resolveArray(GroupDataCollection $child, FakerValueContext $context): array
    {
        $resolved = [];
        foreach ($value as $key => $item) {
            $resolved[$key] = $this->resolveChild($child, $item, $context);
        }
        return $resolved;
    }

    /**
     */
    private function resolveSingle(GroupDataCollection $child, FakerValueContext $context): mixed
    {
        return $this->resolveChild($child, $value, $context, $chooseContext);
    }

    /**
     * Resolve a child collection.
     *
     */
    private function resolveChild(GroupDataCollection $child, FakerValueContext $context, ChooseSerializeContext $chooseContext): mixed
    {
        return $context->propertyToArrayResolver->resolve($chooseContext, $child, $value);
    }

    private function hasCollectObjectType(DataCollection $collection): bool
    {
        foreach ($collection->getTypes() as $type) {
            if ($type->kind->existsFakerClass()) {
                return true;
            }
        }
        return false;
    }

    private function findCollectObjectType(DataCollection $collection): ?TypeCollection
    {
        foreach ($collection->getTypes() as $type) {
            if ($type->kind->existsFakerClass()) {
                return $type;
            }
        }
        return null;
    }
}
