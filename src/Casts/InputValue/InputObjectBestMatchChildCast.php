<?php

namespace Astral\Serialize\Casts\InputValue;

use Astral\Serialize\Casts\InputValue\Trait\InputArrayTrait;
use Astral\Serialize\Contracts\Attribute\InputValueCastInterface;
use Astral\Serialize\Enums\TypeKindEnum;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Collections\GroupDataCollection;
use Astral\Serialize\Support\Context\InputValueContext;
use ReflectionException;

class InputObjectBestMatchChildCast implements InputValueCastInterface
{
    use InputArrayTrait;

    // 对象数组  唯一数组  联合数组
    public function match($value, DataCollection $collection, InputValueContext $context): bool
    {
        return $value
            && (is_object($value) || (is_array($value) && $this->getDimension($value) === 1))
            && count($collection->getChildren()) > 1 && $this->hasObjectType($collection);
    }

    /**
     * @param $value
     * @param DataCollection $collection
     * @param InputValueContext $context
     * @return mixed
     * @throws ReflectionException
     */
    public function resolve($value, DataCollection $collection, InputValueContext $context): mixed
    {

        $children       = $collection->getChildren();
        $bestMatchClass = $this->getBestMatchClass($collection, $children, $context, (array)$value);

        if (!$bestMatchClass) {
            return $value;
        }

        $child     = $children[$bestMatchClass];
        $collection->getTypeTo($child->getClassName());

        return $this->resolveSingle($value, $child, $collection, $context);

    }

    /**
     * @param DataCollection $collection
     * @param GroupDataCollection[] $children
     * @param InputValueContext $context
     * @param array $value
     * @return string|null
     */
    private function getBestMatchClass(DataCollection $collection, array $children, InputValueContext $context, array $value): ?string
    {
        $valueKeys    = array_flip(array_keys($value));
        $bestMatch    = null;
        $highestScore = -1;

        $groups       = $context->chooseSerializeContext->getGroups();
        $defaultGroup = $context->chooseSerializeContext->serializeClass;

        foreach ($collection->getTypes() as $type) {
            if ($type->kind !== TypeKindEnum::CLASS_OBJECT) {
                continue;
            }

            $score = 0;
            foreach ($children[$type->className]->getPropertiesInputNamesByGroups($groups, $defaultGroup) as $property) {
                if (isset($valueKeys[$property])) {
                    $score++;
                }
            }

            if ($score > $highestScore) {
                $highestScore = $score;
                $bestMatch    = $type->className;
            }

        }

        return $bestMatch;
    }

    private function hasObjectType(DataCollection $collection): bool
    {
        foreach ($collection->getTypes() as $type) {
            if ($type->kind === TypeKindEnum::CLASS_OBJECT) {
                return true;
            }
        }
        return false;
    }
}
