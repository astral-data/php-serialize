<?php

declare(strict_types=1);

namespace Astral\Serialize\OpenApi\Storage\OpenAPI;

use Astral\Serialize\OpenApi\Storage\StorageInterface;

/**
 * 参数配置
 */
class ParameterStorage implements StorageInterface
{
    private $data = [];

    /** @var string 参数类型 query|header|cookie */
    public string $in = 'query';

    /** @var string 参数名 */
    public string $name;

    /** @var string 参数介绍 */
    public string $description;

    /** @var bool 这个参数是否必须 */
    public bool $required = true;

    /** @var SchemaStorage 参数类型 */
    public SchemaStorage $schema;

    public function addProperties(string $name, string $type, string $description, string $example, bool $required = false): void
    {
        $this->data[] = [
            'in'          => 'query',
            'name'        => $name,
            'description' => $description,
            'schema'      => ['type' => $type],
            'example'     => $example,
            'required'    => $required,
        ];
    }

    public function addHeaderProperties(string $name, string $description, string $example): void
    {
        $this->data[] = [
            'in'          => 'header',
            'name'        => $name,
            'description' => $description,
            'schema'      => ['type' => 'string'],
            'example'     => $example,
            'required'    => false,
        ];
    }

    public function deleteHeadersProperties(array $names = []): void
    {
        if (! $names) {
            return;
        }

        foreach ($this->data as $key => ['in' => $type,'name' => $headerName]) {
            if ($type === 'header' && in_array($headerName, $names, true)) {
                unset($this->data[$key]);
                break;
            }
        }
    }

    public function getData(): array
    {
        return $this->data;
    }
}
