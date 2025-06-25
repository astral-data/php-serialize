<?php

namespace Astral\Serialize\Casts\InputValue;

use Astral\Serialize\Casts\InputValue\Trait\InputArrayTrait;
use Astral\Serialize\Contracts\Attribute\InputValueCastInterface;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Context\InputValueContext;
use ReflectionException;

class InputArraySingleChildCast implements InputValueCastInterface
{
    use InputArrayTrait;

    public function match($value, DataCollection $collection, InputValueContext $context): bool
    {
        return is_array($value)
            && count($collection->getChildren()) === 1
            && $this->hasCollectObjectType($collection);
    }

    /**
     * Resolve the input value for the given collection and context.
     * @throws ReflectionException
     */
    public function resolve(mixed $value, DataCollection $collection, InputValueContext $context): mixed
    {
        $child     = current($collection->getChildren());
        $childType = $collection->getTypes()[0];

        $context->chooseSerializeContext->getProperty($collection->getName())?->setType($childType);

        if ($childType->kind->isCollect()) {
            return $this->resolveArray($value, $child, $collection, $context);
        }

        return $this->resolveSingle($value, $child, $collection, $context);
    }
}
