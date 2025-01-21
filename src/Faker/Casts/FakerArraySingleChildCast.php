<?php

namespace Astral\Serialize\Faker\Casts;

use Astral\Serialize\Support\Context\FakerValueContext;
use Astral\Serialize\Casts\InputValue\Trait\InputArrayTrait;
use Astral\Serialize\Support\Collections\GroupDataCollection;
use Astral\Serialize\Contracts\Attribute\InputValueCastInterface;
use Astral\Serialize\Exceptions\NotFoundAttributePropertyResolver;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Context\InputValueContext;
use ReflectionException;

class FakerArraySingleChildCast implements InputValueCastInterface
{
    use InputArrayTrait;

    public function match(DataCollection $collection, FakerValueContext $context): bool
    {
        return count($collection->getChildren()) === 1
            && $this->hasCollectObjectType($collection);
    }

    /**
     * Resolve the input value for the given collection and context.
     * @throws NotFoundAttributePropertyResolver
     * @throws ReflectionException
     */
    public function resolve(DataCollection $collection, FakerValueContext $context): mixed
    {
        $child     = current($collection->getChildren());
        $childType = $collection->getTypes()[0];


        if ($childType->kind->isCollect()) {
            return $this->resolveArray($value, $child, $collection, $context);
        }

        return $this->resolveSingle($value, $child, $collection, $context);
    }

    /**
     * @throws ReflectionException
     * @throws NotFoundAttributePropertyResolver
     */
    private function resolveArray(GroupDataCollection $child, DataCollection $collection, InputValueContext $context): array
    {
        $resolved = [];
        foreach ($value as $key => $item) {
            $chooseContext  = $this->createChooseContext($context, $collection, $key);
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
}
