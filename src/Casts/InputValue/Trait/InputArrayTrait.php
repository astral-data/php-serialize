<?php

namespace Astral\Serialize\Casts\InputValue\Trait;

use ReflectionException;
use Astral\Serialize\Support\Context\InputValueContext;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Context\ChooseSerializeContext;
use Astral\Serialize\Support\Collections\GroupDataCollection;
use Astral\Serialize\Exceptions\NotFoundAttributePropertyResolver;

trait InputArrayTrait
{
    /**
     * @throws ReflectionException
     * @throws NotFoundAttributePropertyResolver
     */
    private function resolveArray(array $value, GroupDataCollection $child, DataCollection $collection, InputValueContext $context): array
    {
        $resolved = [];
        foreach ($value as $key => $item) {
            $chooseContext = $this->createChooseContext($context, $collection, $key);
            $resolved[$key] = $this->resolveChild($child, $item, $context, $chooseContext);
        }
        return $resolved;
    }


    /**
     * @throws NotFoundAttributePropertyResolver
     * @throws ReflectionException
     */
    private function resolveSingle(mixed $value, GroupDataCollection $child, DataCollection $collection, InputValueContext $context): mixed
    {
        $chooseContext = $this->createChooseContext($context, $collection);
        return $this->resolveChild($child, $value, $context, $chooseContext);
    }

    /**
     * Resolve a child collection.
     *
     * @throws NotFoundAttributePropertyResolver
     * @throws ReflectionException
     */
    private function resolveChild(GroupDataCollection $child, mixed $value, InputValueContext $context, ChooseSerializeContext $chooseContext): mixed
    {
        return $context->propertyInputValueResolver->resolve($chooseContext, $child, $value);
    }

    /**
     * Create a new ChooseSerializeContext.
     */
    private function createChooseContext(InputValueContext $context, DataCollection $collection, $key = null): ChooseSerializeContext
    {
        $chooseContext = new ChooseSerializeContext();
        $chooseContext->groups = $context->chooseSerializeContext->groups;

        if ($key !== null) {
            $context->chooseSerializeContext
                ->getProperty($collection->getName())
                ->addChildren($chooseContext, $key);
        } else {
            $context->chooseSerializeContext
                ->getProperty($collection->getName())
                ->addChildren($chooseContext);
        }

        return $chooseContext;
    }

    private function hasObjectType(DataCollection $collection): bool
    {
        foreach ($collection->getTypes() as $type) {
            if ($type->kind->existsClass()) {
                return true;
            }
        }
        return false;
    }

    //    /**
    //     * Check if the collection contains an object type.
    //     */
    //    private function hasObjectType(DataCollection $collection): bool
    //    {
    //        return array_reduce($collection->getTypes(), fn ($carry, $type) => $carry || $type->kind->existsClass(), false);
    //    }
}
