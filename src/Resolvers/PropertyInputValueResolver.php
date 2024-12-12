<?php

namespace Astral\Serialize\Resolvers;

use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Collections\DataGroupCollection;
use Astral\Serialize\Support\Collections\TypeCollection;
use Illuminate\Support\Collection;

class PropertyInputValueResolver
{
    public function resolve(object|string $serialize, DataGroupCollection $collection, array $payload): mixed
    {

        $object = is_string($serialize) ? new $serialize() : $serialize;

        foreach ($payload as $name => $value) {

            $dataCollection = $collection->getPropertyTo($name);

            if(!$dataCollection || $dataCollection->getInputIgnore()) {
                continue;
            }

            $object->{$dataCollection->getName()} = $this->resolveValue($dataCollection, $value);
        }

        return  $object;

    }

    private function resolveValue(DataCollection $collection, $value): mixed
    {
        if(is_array($value) && collect($collection->getType())->first(fn (TypeCollection $e) => $e->kind->existsClass())) {

            /** @var Collection<DataGroupCollection> $children */
            $children = collect($collection->getChildren());

            if($children->count() === 1) {
                return $this->resolve($children->first()->getClassName(), $children->first()->getClassName(), $value);
            }

            $bestMatch = $children
                ->mapWithKeys(function (DataGroupCollection $child) use ($value) {
                    $fields     = $child->getPropertiesName(); // 假设子类有一个方法获取字段名列表
                    $matchScore = count(array_intersect($fields, array_keys($value)));
                    return [$child->getClassName() => $matchScore];
                })
                ->sortDesc() // 按匹配度降序排列
                ->keys()
                ->first(); // 获取匹配度最高的子元素

            if (!$bestMatch) {
                return null;
            }

            $child = $children->first(fn ($c) => $c->getClassName() === $bestMatch);
            return $this->resolve($child->getClassName(), $child, $value);
        }
    }
}
