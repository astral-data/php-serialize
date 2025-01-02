<?php

namespace Astral\Serialize\Casts\InputValue;

use Astral\Serialize\Casts\InputValue\Trait\InputArrayTrait;
use Astral\Serialize\Contracts\Attribute\InputValueCastInterface;
use Astral\Serialize\Enums\TypeKindEnum;
use Astral\Serialize\Exceptions\NotFoundAttributePropertyResolver;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Collections\GroupDataCollection;
use Astral\Serialize\Support\Context\InputValueContext;
use ReflectionException;

class InputArrayBestMatchChildCast implements InputValueCastInterface
{
    use InputArrayTrait;

    public function match($value, DataCollection $collection, InputValueContext $context): bool
    {
        return $value && is_array($value) && count($collection->getChildren()) > 1 && $this->hasObjectType($collection);
    }

    /**
     * @param $value
     * @param DataCollection $collection
     * @param InputValueContext $context
     * @return mixed
     * @throws NotFoundAttributePropertyResolver
     * @throws ReflectionException
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

        $context->chooseSerializeContext->getProperty($collection->getName())->setType($childType);

        if ($childType->kind === TypeKindEnum::COLLECT_OBJECT) {
            return $this->resolveArray($value, $child, $collection, $context);
        }

        return $this->resolveSingle($value, $child, $collection, $context);

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
}
