<?php

declare(strict_types=1);

namespace Astral\Serialize\OpenApi\Storage\OpenAPI;

use Astral\Serialize\OpenApi\Storage\StorageInterface;

/**
 * 参数配置
 */
class ResponseStorage implements StorageInterface
{
    /** @var RequestBodyStorage 参数类型 */
    public RequestBodyStorage $content;

    public function __construct(
        public string $contentType = 'application/json',
        public string $description = '',
        public ?string $group = null
    ) {
        $this->content[$this->contentType]['schema'] = [];
    }

    public function withParameter(SchemaStorage $schema): void
    {
        $this->content[$this->contentType]['schema'] = $schema->getData();
    }
}
