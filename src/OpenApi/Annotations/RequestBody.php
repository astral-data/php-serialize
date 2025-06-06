<?php

declare(strict_types=1);

namespace Astral\Serialize\OpenApi\Annotations;

use Astral\Serialize\OpenApi\Enum\ContentTypeEnum;
use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class RequestBody
{
    public function __construct(
        /** @var class-string $className */
        public string $className = '',
        public ContentTypeEnum $contentType = ContentTypeEnum::JSON,
        public array|null $group = null
    ){
    }
}
