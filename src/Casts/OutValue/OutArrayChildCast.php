<?php

namespace Astral\Serialize\Casts\OutValue;

use Astral\Serialize\Contracts\Attribute\OutValueCastInterface;
use Astral\Serialize\Enums\TypeKindEnum;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Collections\GroupDataCollection;
use Astral\Serialize\Support\Context\ChooseSerializeContext;
use Astral\Serialize\Support\Context\OutContext;

class OutArrayChildCast implements OutValueCastInterface
{
    public function match($value, DataCollection $collection, OutContext $context): bool
    {
        return in_array($context->chooseSerializeContext->getProperty($collection->getName())?->getType()?->kind, [TypeKindEnum::COLLECT_SINGLE_OBJECT,TypeKindEnum::OBJECT]);
    }

    /**
     * Resolve the input value for the given collection and context.
     */
    public function resolve(mixed $value, DataCollection $collection, OutContext $context): mixed
    {
        $child     = current($collection->getChildren());
        $childType = $collection->getTypes()[0];

        $context->chooseSerializeContext->getProperty($collection->getName())->setType($childType);

        $choose = $context->chooseSerializeContext->getProperty($collection->getName())->getChildren();

        if ($childType->kind === TypeKindEnum::COLLECT_SINGLE_OBJECT) {
            return $this->resolveArray($value, $child, $context, $choose);
        }

        return $this->resolveSingle($value, $child, $context, current($choose));
    }

    /**
     */
    private function resolveArray(array $value, GroupDataCollection $child, OutContext $context, array $chooseContext): array
    {
        $resolved = [];
        foreach ($value as $key => $item) {
            $resolved[$key] = $this->resolveChild($child, $item, $context, $chooseContext[$key]);
        }
        return $resolved;
    }

    /**
     */
    private function resolveSingle(mixed $value, GroupDataCollection $child, OutContext $context, ChooseSerializeContext $chooseContext): mixed
    {
        return $this->resolveChild($child, $value, $context, $chooseContext);
    }

    /**
     * Resolve a child collection.
     *
     */
    private function resolveChild(GroupDataCollection $child, mixed $value, OutContext $context, ChooseSerializeContext $chooseContext): mixed
    {
        return $context->propertyToArrayResolver->resolve($chooseContext, $child, $value);
    }
}
