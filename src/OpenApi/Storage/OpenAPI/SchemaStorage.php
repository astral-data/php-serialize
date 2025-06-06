<?php

declare(strict_types=1);

namespace Astral\Serialize\OpenApi\Storage\OpenAPI;

use Astral\Serialize\OpenApi\Collections\ParameterCollection;
use Astral\Serialize\OpenApi\Storage\StorageInterface;
use Exception;

/**
 * 文档开发者介绍
 */
class SchemaStorage implements StorageInterface
{
    private array|SchemaStorage $data = [
        'type'       => 'object',
        'properties' => [],
    ];

    public function getData(): SchemaStorage|array
    {
        return $this->data;
    }

    /**
     * Undocumented function
     *
     * @param array<string,ParameterCollection> $tree
     * @param mixed|null $node
     * @return SchemaStorage
     */
    public function build(array $tree, mixed &$node = null): static
    {
        if ($node === null) {
            $node = &$this->data;
        }

        foreach ($tree as $item) {

            if ($item->ignore) {
                continue;
            }

            $node['properties'][$item->name] = [
                'type'        => strtolower($item->type->getOpenApiName()),
                'description' => $item->descriptions,
                'example'     => $item->example,
            ];

            if ($item->required) {
                $node['required'][] = $item->name;
            }

            if ($item->children) {
                // list对象
                if ($item->type->isCollect()) {
                    $node['properties'][$item->name]['items'] = [
                        'type'       => 'object',
                        'properties' => [],
                    ];
                    $tree = &$node['properties'][$item->name]['items'];
                }
                // 单个对象
                elseif ($item->type->existsCollectClass()) {
                    $node['properties'][$item->name] = [
                        'type'       => 'object',
                        'properties' => [],
                    ];
                    $tree = &$node['properties'][$item->name];
                }

                foreach ($item->children as $v){
                    $this->build($v, $tree);
                }

            }
        }

        return $this;
    }

    public function addProperties(string $name, string $type, string $description, string $example, bool $required = false): void
    {
        $this->data['properties'][$name] = [
            'type'        => $type,
            'description' => $description,
            'example'     => $example,
        ];

        if ($required) {
            $this->data['required'][] = $name;
        }
    }

}
