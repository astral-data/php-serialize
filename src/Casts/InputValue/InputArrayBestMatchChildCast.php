<?php

namespace Astral\Serialize\Casts\InputValue;

use Astral\Serialize\Contracts\Attribute\InputValueCastInterface;
use Astral\Serialize\Enums\TypeKindEnum;
use Astral\Serialize\Exceptions\NotFoundAttributePropertyResolver;
use Astral\Serialize\Resolvers\PropertyInputValueResolver;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Collections\GroupDataCollection;
use Astral\Serialize\Support\Context\InputValueContext;
use ReflectionException;

class InputArrayBestMatchChildCast implements InputValueCastInterface
{
    public function match($value, DataCollection $collection, InputValueContext $context): bool
    {
        return $value && is_array($value) && count($collection->getChildren()) > 1 && $this->hasObjectType($collection);
    }

    /**
     * @throws NotFoundAttributePropertyResolver
     */
    public function resolve($value, DataCollection $collection, InputValueContext $context): mixed
    {
        $children       = $collection->getChildren();
        $bestMatchClass = $this->getBestMatchClass($children, $value);

        if (!$bestMatchClass) {
            return $value;
        }

        $child     = $this->findChildByClass($children, $bestMatchClass);
        $childType = $collection->getTypeTo($child->getClassName());
        $collection->setChooseType($childType);

        if ($childType->kind === TypeKindEnum::COLLECT_OBJECT) {
            return array_map(fn ($item) => $this->resolveChild(
                propertyInputValueResolver: $context->propertyInputValueResolver,
                //                className: $child->getClassName(),
                child: $child,
                value: $item
            ), $value);
        }

        return $this->resolveChild(
            propertyInputValueResolver: $context->propertyInputValueResolver,
            //            className: $child->getClassName(),
            child: $child,
            value: $value
        );
    }

    /**
     * @param GroupDataCollection[] $children
     * @param array $value
     * @return string|null
     */
    private function getBestMatchClass(array $children, array $value): ?string
    {
        // 根据属性名匹配计算得分
        $valueKeys    = array_flip(array_keys($value));
        $bestMatch    = null;
        $highestScore = -1;

        foreach ($children as $child) {
            $score = 0;
            foreach ($child->getPropertiesName() as $property) {
                if (isset($valueKeys[$property])) {
                    $score++;
                }
            }

            if ($score > $highestScore) {
                $highestScore = $score;
                $bestMatch    = $child->getClassName();
            }
        }

        return $bestMatch;
    }

    private function findChildByClass(array $children, string $className): ?GroupDataCollection
    {
        foreach ($children as $child) {
            if ($child->getClassName() === $className) {
                return $child;
            }
        }

        return null;
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

    /**
     * @throws NotFoundAttributePropertyResolver
     * @throws ReflectionException
     */
    private function resolveChild(PropertyInputValueResolver $propertyInputValueResolver, GroupDataCollection $child, $value): mixed
    {
        return $propertyInputValueResolver->resolve($child, $value);
    }
}
