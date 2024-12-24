<?php

namespace Astral\Serialize\Casts\InputValue;

use Astral\Serialize\Contracts\Attribute\InputValueCastInterface;
use Astral\Serialize\Enums\TypeKindEnum;
use Astral\Serialize\Exceptions\NotFoundAttributePropertyResolver;
use Astral\Serialize\SerializeContainer;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Collections\DataGroupCollection;
use Astral\Serialize\Support\Context\InputValueContext;

class InputArraySingleChildCast implements InputValueCastInterface
{
    public function match($value, DataCollection $collection, InputValueContext $context): bool
    {
        return $value && is_array($value) && count($collection->getChildren()) === 1 && $this->hasObjectType($collection);
    }

    /**
     * @throws NotFoundAttributePropertyResolver
     */
    public function resolve($value, DataCollection $collection, InputValueContext $context): mixed
    {

        $child     = current($collection->getChildren());
        $childType = $collection->getTypes()[0];

        $collection->setChooseType($childType);


        if ($childType->kind === TypeKindEnum::COLLECT_OBJECT) {
            return array_map(fn ($item) => $this->resolveChild($child->getClassName(), $child, $item), $value);
        }

        return $this->resolveChild($child->getClassName(), $child, $value);
    }

    /**
     * @throws NotFoundAttributePropertyResolver
     */
    private function resolveChild(string $className, DataGroupCollection $child, $value): mixed
    {
        return SerializeContainer::get()->propertyInputValueResolver()->resolve($className, $child, $value);
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
}
