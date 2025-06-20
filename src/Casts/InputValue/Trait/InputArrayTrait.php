<?php

namespace Astral\Serialize\Casts\InputValue\Trait;

use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Collections\GroupDataCollection;
use Astral\Serialize\Support\Context\ChooseSerializeContext;
use Astral\Serialize\Support\Context\InputValueContext;
use ReflectionException;

trait InputArrayTrait
{
    /**
     * @throws ReflectionException
     */
    private function resolveArray(array $value, GroupDataCollection $child, DataCollection $collection, InputValueContext $context): array
    {
        $resolved = [];
        foreach ($value as $key => $item) {
            $chooseContext  = $this->createChooseContext($context, $collection, $key);
            $resolved[$key] = $this->resolveChild($child, $item, $context, $chooseContext);
        }
        return $resolved;
    }

    /**
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
        $chooseContext         = new ChooseSerializeContext($context->chooseSerializeContext->serializeClass);
        $chooseContext->setGroups($context->chooseSerializeContext->getGroups());

        if ($key !== null) {
            $context->chooseSerializeContext
                ->getProperty($collection->getName())
                ?->addChildren($chooseContext, $key);
        } else {
            $context->chooseSerializeContext
                ->getProperty($collection->getName())
                ?->addChildren($chooseContext);
        }

        return $chooseContext;
    }

    private function hasCollectObjectType(DataCollection $collection): bool
    {
        foreach ($collection->getTypes() as $type) {
            if ($type->kind->existsCollectClass()) {
                return true;
            }
        }
        return false;
    }

    public function getDimension(array $array): int
    {
        foreach ($array as $value) {
            if (is_array($value)) {
                return 2;
            }
        }
        return 1;
    }
}
