<?php

namespace Astral\Serialize\Cast\InputValue;

use Astral\Serialize\Enums\TypeKindEnum;
use Astral\Serialize\SerializeContainer;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Collections\DataGroupCollection;
use Astral\Serialize\Contracts\Attribute\InputValueCastInterface;
use Astral\Serialize\Exceptions\NotFoundAttributePropertyResolver;
use Astral\Serialize\Contracts\Resolve\Strategies\ResolveStrategyInterface;

class InputValueSingleChildCast implements InputValueCastInterface
{
    public function match($value, DataCollection $collection): bool
    {
        return is_array($value) && count($collection->getChildren()) === 1 && $this->hasObjectType($collection);
    }

    /**
     * @throws NotFoundAttributePropertyResolver
     */
    public function resolve($value, DataCollection $collection): mixed
    {

        $child = current($collection->getChildren());
        $childType = $collection->getType()[0];

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
        foreach ($collection->getType() as $type) {
            if ($type->kind->existsClass()) {
                return true;
            }
        }
        return false;
    }
}
