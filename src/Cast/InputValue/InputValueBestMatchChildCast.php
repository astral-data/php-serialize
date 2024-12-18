<?php

namespace Astral\Serialize\Cast\InputValue;

use Astral\Serialize\Enums\TypeKindEnum;
use Astral\Serialize\SerializeContainer;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Collections\DataGroupCollection;
use Astral\Serialize\Contracts\Attribute\InputValueCastInterface;
use Astral\Serialize\Exceptions\NotFoundAttributePropertyResolver;
use Astral\Serialize\Contracts\Resolve\Strategies\ResolveStrategyInterface;

class InputValueBestMatchChildCast implements InputValueCastInterface
{
    public function match($value, DataCollection $collection): bool
    {
        return is_array($value) && count($collection->getChildren()) > 1 && $this->hasObjectType($collection);
    }

    /**
     * @throws NotFoundAttributePropertyResolver
     */
    public function resolve($value, DataCollection $collection): mixed
    {
        $children = $collection->getChildren();
        $bestMatchClass = $this->getBestMatchClass($children, $value);

        if (!$bestMatchClass) {
            return $value;
        }

        $child = $this->findChildByClass($children, $bestMatchClass);
        $childType = $collection->getTypeTo($child->getClassName());
        $collection->setChooseType($childType);

        if ($childType->kind === TypeKindEnum::COLLECT_OBJECT) {
            return array_map(fn ($item) => $this->resolveChild($child->getClassName(), $child, $item), $value);
        }

        return $this->resolveChild($child->getClassName(), $child, $value);
    }

    /**
     * @param DataGroupCollection[] $children
     * @param array $value
     * @return string|null
     */

    private function getBestMatchClass(array $children, array $value): ?string
    {
        // 根据属性名匹配计算得分
        $valueKeys = array_flip(array_keys($value));
        $bestMatch = null;
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
                $bestMatch = $child->getClassName();
            }
        }

        return $bestMatch;
    }


    private function findChildByClass(array $children, string $className): ?DataGroupCollection
    {
        foreach ($children as $child) {
            if ($child->getClassName() === $className) {
                return $child;
            }
        }

        return null;
    }

    /**
     * @throws NotFoundAttributePropertyResolver
     */
    private function resolveChild(string $className, DataGroupCollection $child, $value): mixed
    {
        // 调用解析器或解析逻辑
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
