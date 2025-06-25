<?php

declare(strict_types=1);

namespace Astral\Serialize\OpenApi\Storage\OpenAPI;

use Astral\Serialize\Enums\TypeKindEnum;
use Astral\Serialize\OpenApi\Annotations\OpenApi;
use Astral\Serialize\OpenApi\Collections\ParameterCollection;
use Astral\Serialize\OpenApi\Enum\ParameterTypeEnum;
use Astral\Serialize\OpenApi\Storage\StorageInterface;

/**
 * OpenAPI Schema 数据存储和构建器
 * 用于生成符合 OpenAPI 规范的 Schema 结构
 */
class SchemaStorage implements StorageInterface
{
    /**
     * Schema 数据结构
     */
    private array $data = [
        'type'       => 'object',
        'properties' => [],
    ];

    /**
     * 获取构建的 Schema 数据
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * 构建 OpenAPI Schema 数据结构
     *
     * @param array<ParameterCollection> $parameterTree 参数集合树
     * @param array|null $currentNode 当前构建节点的引用
     * @param-out array $currentNode 当前构建节点的引用
     * @return static
     */
    public function build(array $parameterTree, array|null &$currentNode = null): static
    {
        if ($currentNode === null) {
            $currentNode = &$this->data;
        }

        foreach ($parameterTree as $parameter) {


            // 跳过被标记为忽略的参数
            if ($parameter->ignore) {
                continue;
            }

            // 构建基础属性 Schema
            $this->buildBasicPropertySchema($parameter, $currentNode);

            // oneOf/anyOf/allOf 格式
            if ($parameter->type->isOf()) {
                $this->buildOfProperties($parameter, $currentNode);
            }
            // 处理嵌套子属性
            elseif ($parameter->children) {
                $this->buildNestedProperties($parameter, $currentNode);
            }
        }

        return $this;
    }

    /**
     * 构建基础属性的 Schema 结构
     */
    private function buildBasicPropertySchema(ParameterCollection $parameter, array &$currentNode): void
    {
        $propertyName = $parameter->name;

        $currentNode['properties'][$propertyName] = [
            'type'        => $parameter->type->value,
            'description' => $this->getDescriptions($parameter),
            'example'     => $parameter->openApiAnnotation?->example,
        ];

        // 添加必填字段标记
        if ($parameter->required) {
            $currentNode['required'][] = $propertyName;
        }
    }

    /**
     * 构建 oneOf/anyOf/allOf 属性结构
     */
    public function buildOfProperties(ParameterCollection $topParameter, array &$currentNode): void
    {
        $propertyName = $topParameter->name;
        // 重构属性结构为 oneOf/anyOf/allOf 格式
        $node = &$currentNode['properties'][$propertyName][$topParameter->type->value];

        $i          = 0;
        $addedTypes = [];
        foreach ($topParameter->types as $kindType) {
            $type = ParameterTypeEnum::getBaseEnumByTypeKindEnum($kindType);
            if ($type && !in_array($type->value, $addedTypes, true)) {
                $node[$i]     = ['type' => $type->value];
                $addedTypes[] = $type->value;
                $i++;
            }
        }

        if ($topParameter->children) {
            foreach ($topParameter->children as $className => $child) {
                $type = ParameterTypeEnum::getArrayAndObjectEnumBy($topParameter->types, $className);
                if ($type->isObject()) {
                    $node[$i]  = ['type' => 'object','properties' => []];
                    $childNode = &$node[$i];
                    $i++;
                } elseif ($type->isArray()) {
                    $node[$i]  = ['type' => 'array','items' => ['type' => 'object','properties' => []]];
                    $childNode = &$node[$i]['items'];
                    $i++;
                }

                $this->build($child, $childNode);
            }
        }

    }

    /**
     * 构建嵌套属性结构
     */
    private function buildNestedProperties(ParameterCollection $topParameter, array &$currentNode): void
    {
        $propertyName = $topParameter->name;
        $nestedNode   = null;

        if ($topParameter->type->isArray()) {
            // 数组类型：创建 items 结构
            $currentNode['properties'][$propertyName]['items'] = [
                'type'       => 'object',
                'properties' => [],
            ];
            $nestedNode = &$currentNode['properties'][$propertyName]['items'];
        } elseif ($topParameter->type->isObject()) {
            // 对象类型：重构为嵌套对象结构
            $currentNode['properties'][$propertyName] = [
                'type'        => 'object',
                'properties'  => [],
                'description' => $this->getDescriptions($topParameter),
            ];
            $nestedNode = &$currentNode['properties'][$propertyName];
        }

        // 递归构建子属性
        if ($nestedNode !== null) {
            foreach ($topParameter->children as $childParameter) {
                $this->build($childParameter, $nestedNode);
            }
        }
    }

    public function getDescriptions(ParameterCollection $parameter): string
    {
        $description = $parameter->openApiAnnotation->description ?? '';
        if (!ParameterTypeEnum::hasEnum($parameter->types)) {
            return  $description;
        }

        $names = [];
        foreach ($parameter->types as $type) {
            if (TypeKindEnum::ENUM === $type->kind && enum_exists($type->className)) {
                foreach ($type->className::cases() as $case) {
                    $names[$case->name] = $case->name;
                }
            }
        }

        $description .= ($description ? ' ' : '') . 'optional values：' . implode('、', $names);

        return $description;
    }
}
