<?php

namespace Astral\Serialize\Resolvers;

use Astral\Serialize\Exceptions\NotFoundAttributePropertyResolver;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Collections\DataGroupCollection;
use Astral\Serialize\Support\Collections\TypeCollection;
use Astral\Serialize\Support\Config\ConfigManager;
use Illuminate\Support\Collection;

class PropertyInputValueResolver
{
    public function __construct(
        private readonly ConfigManager $configManager,
        private readonly InputValueCastResolver $inputValueCastResolver,
    ) {

    }

    /**
     * @throws NotFoundAttributePropertyResolver
     */
    public function resolve(string|object $serialize, DataGroupCollection $groupCollection, array $payload): object
    {
        // 获取对象实例
        $object = is_string($serialize) ? new $serialize() : $serialize;

        $payloadKeys = array_keys($payload);

        // 遍历所有属性集合
        foreach ($groupCollection->getProperties() as $collection) {

            // 跳过需要忽略的输入
            if ($collection->getInputIgnore()) {
                continue;
            }

            $inputName = $this->inputNameValid($collection, $payloadKeys);

            if ($inputName === false) {
                continue;
            }


            $resolvedValue = $this->resolveValue($collection, $payload[$inputName]);

            // exec input value cast
            foreach ($this->configManager->getInputValueCasts() as $cast) {
                $resolvedValue = $cast->resolve($resolvedValue, $collection);
            }
            $this->inputValueCastResolver->resolve($resolvedValue, $collection);

            $object->{$collection->getName()} = $resolvedValue;

        }

        return $object;
    }

    private function inputNameValid(DataCollection $collection, array &$payloadKeys): false|string
    {
        $inputNames = $collection->getInputName();


        if (!$inputNames && in_array($collection->getName(), $payloadKeys)) {
            unset($payloadKeys[$collection->getName()]);
            return $collection->getName();
        }


        if (count($inputNames) === 1 && in_array($inputNames[0], $payloadKeys)) {
            unset($payloadKeys[$inputNames[0]]);
            return $collection->getName();
        }

        $intersect = array_intersect($inputNames, $payloadKeys)[0] ?? false;
        if ($intersect !== false) {
            unset($payloadKeys[$intersect]);
            return $intersect;
        }

        return false;
    }

    /**
     * @throws NotFoundAttributePropertyResolver
     */
    private function resolveValue(DataCollection $collection, $value): mixed
    {
        if ($value && is_array($value) && collect($collection->getType())->first(fn (TypeCollection $e) => $e->kind->existsClass())) {

            /** @var Collection<DataGroupCollection> $children */
            $children = collect($collection->getChildren());

            if ($children->count() === 1) {
                $resolveClass = $children->first(fn ($e) => $e->getClassName());
                return $this->resolve($resolveClass, $resolveClass, $value);
            }

            $bestMatchClass = $children
                ->mapWithKeys(function (DataGroupCollection $child) use ($value) {
                    $fields     = $child->getPropertiesName(); // 假设子类有一个方法获取字段名列表
                    $matchScore = count(array_intersect($fields, array_keys($value)));
                    return [$child->getClassName() => $matchScore];
                })
                ->sortDesc() // 按匹配度降序排列
                ->keys()
                ->first(); // 获取匹配度最高的子元素

            if (!$bestMatchClass) {
                return $value;
            }

            $child = $children->first(fn ($e) => $e->getClassName() === $bestMatchClass);
            return $this->resolve($bestMatchClass, $child, $value);
        }

        return $value;
    }
}
