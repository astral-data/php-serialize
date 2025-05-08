<?php

declare(strict_types=1);

namespace Astral\Serialize\OpenApi\Storage\OpenAPI;

use Astral\Serialize\OpenApi\Enum\ContentTypeEnum;
use Astral\Serialize\OpenApi\Storage\StorageInterface;

/**
 * 参数配置
 */
class RequestBodyStorage implements StorageInterface
{
    public function __construct(
        public ContentTypeEnum $contentType = ContentTypeEnum::JSON,
        /** @var array<string,SchemaStorage> 参数类型 */
        public array $content = []
    ) {
        $this->content[$this->contentType->value]['schema'] = [];
    }

    public function withParameter(SchemaStorage $schema): void
    {
        $this->content[$this->contentType->value]['schema'] = $schema->getData();
    }
}
