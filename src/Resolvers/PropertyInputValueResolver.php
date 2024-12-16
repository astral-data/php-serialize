<?php

namespace Astral\Serialize\Resolvers;

use InvalidArgumentException;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Collections\DataGroupCollection;
use Astral\Serialize\Support\Collections\TypeCollection;
use Illuminate\Support\Collection;

class PropertyInputValueResolver
{
    public function resolve(object|string $serialize, DataGroupCollection $groupCollection, array $payload): object
    {
        // 获取对象实例
        $object = is_string($serialize) ? new $serialize() : $serialize;

        // 获取 payload 的所有键
        $payloadKeys = array_keys($payload);

        // 遍历所有属性集合
        foreach ($groupCollection->getProperties() as $collection) {
            // 跳过需要忽略的输入
            if ($collection->getInputIgnore()) {
                continue;
            }

            // 检查 getInputName 的所有值是否都存在于 payloadKeys 中
            $inputNames    = $collection->getInputName();
            $matchedValues = array_intersect_key($payload, array_flip($inputNames));
            // 如果没有完全匹配，进行处理
            if (count($matchedValues) !== count($inputNames)) {
                continue;
            }

            // 解析值
            $resolvedValue = $this->resolveValue($collection, $matchedValues);
            $propertyName  = $collection->getName();

            // 动态设置对象的属性
            if (property_exists($object, $propertyName)) {
                $object->{$propertyName} = $resolvedValue;
            } else {
                // 如果属性不存在，可以选择抛出异常或者跳过
                throw new InvalidArgumentException("Property '{$propertyName}' does not exist in the given object.");
            }

        }

        return $object;
    }

    private function resolveValue(DataCollection $collection, $value): mixed
    {
        if (is_array($value) && collect($collection->getType())->first(fn (TypeCollection $e) => $e->kind->existsClass())) {

            /** @var Collection<DataGroupCollection> $children */
            $children = collect($collection->getChildren());

            if ($children->count() === 1) {
                $resolveClass = $children->first(fn ($e) => $e->getClassName());
                return $this->resolve($resolveClass, $resolveClass, $value);
            }

            $bestMatchClass = $children
                ->mapWithKeys(function (DataGroupCollection $child) use ($value) {
                    $fields = $child->getPropertiesName(); // 假设子类有一个方法获取字段名列表
                    $matchScore = count(array_intersect($fields, array_keys($value)));
                    return [$child->getClassName() => $matchScore];
                })
                ->sortDesc() // 按匹配度降序排列
                ->keys()
                ->first(); // 获取匹配度最高的子元素

            if (!$bestMatchClass) {
                return null;
            }

            $child = $children->first(fn ($e) => $e->getClassName() === $bestMatchClass);
            return $this->resolve($bestMatchClass, $child, $value);
        }

        return $value;
    }
}
