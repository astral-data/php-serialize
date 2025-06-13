<?php

declare(strict_types=1);

namespace Astral\Serialize\OpenApi\Storage\OpenAPI;

use Astral\Serialize\OpenApi\Enum\ContentTypeEnum;
use Astral\Serialize\OpenApi\Storage\StorageInterface;
use stdClass;

/**
 * 参数配置
 */
class RequestBodyStorage implements StorageInterface
{
    public array|stdClass $parameters = [];

    public function __construct(
        public ContentTypeEnum $contentType = ContentTypeEnum::JSON,
    ) {
    }

    public function withParameter(SchemaStorage $schema): void
    {
        $this->parameters = $schema->getData();
    }

    public function getData(): array
    {
        return [
            'required' => true,
            'content' => [
                $this->contentType->value => [
                    'schema' => $this->parameters
                ]
            ]
        ];
    }
}
