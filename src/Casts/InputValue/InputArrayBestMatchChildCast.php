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

    // 对象数组  唯一数组  联合数组
    public function match($value, DataCollection $collection, InputValueContext $context): bool
    {
        return $value && is_array($value) && $this->getDimension($value) === 2 && count($collection->getChildren()) > 1 && $this->hasCollectObjectType($collection);
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

        $resolved = [];
        $bestClass = [];
        $bestChildren = [];
        foreach ($value as $key => $item) {
            $cacheKey = md5(implode('|', array_keys($item)));

            $bestClass[$cacheKey] ??= $this->getBestMatchClass($collection, $children, $context, $item);
            $bestChildren[$cacheKey] ??= $bestClass[$cacheKey] !== null
                ? $this->findChildByClass($children, $bestClass[$cacheKey])
                : null;

            if ($bestClass[$cacheKey] === null) {
                $resolved[$key] = $item;
            } else {
                $resolved[$key] = $this->resolveSingle($item, $bestChildren[$cacheKey], $collection, $context);
            }
        }
        return $resolved;

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
        $highestScore = 0;

        $groups = $context->chooseSerializeContext->getGroups();
        $defaultGroup = $context->chooseSerializeContext->serializeClass;

        foreach ($collection->getTypes() as $type) {
            if (!$type->kind->isCollect()) {
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
