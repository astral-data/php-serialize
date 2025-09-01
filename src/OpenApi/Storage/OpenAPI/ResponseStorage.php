<?php

declare(strict_types=1);

namespace Astral\Serialize\OpenApi\Storage\OpenAPI;

use Astral\Serialize\OpenApi\Storage\StorageInterface;

class ResponseStorage implements StorageInterface
{
    public array $parameter = [];

    public function __construct(
        public string $contentType = 'application/json',
        public string $description = '成功',
        public string|null $groups = null,
    ) {
    }

    public function withParameter(SchemaStorage $schema): static
    {
        $this->parameter = $schema->getData();
        return $this;
    }

    public function addGlobParameters(array $vols): void
    {
        $dates    = $this->parameter['properties'] ?? [];
        $required = $this->parameter['required']   ?? [];

        $this->parameter['properties'] = [];

        foreach ($vols as $field => $item) {
            if ($item === 'T') {
                $this->parameter['properties'][$field] = [
                    'type'        => 'object',
                    'properties'  => $dates,
                    'required'    => $required,
                ];
            } else {
                $this->parameter['properties'][$field] = [
                    'type'        => 'string',
                    'description' => $item['description'] ?? '',
                    'example'     => $item['example'],
                ];
            }

        }

    }

    public function getData(): array
    {
        return [
            'description' => $this->description,
            'content'     => [
                $this->contentType => [
                    'schema' => $this->parameter
                ]
            ]
        ];
    }
}
