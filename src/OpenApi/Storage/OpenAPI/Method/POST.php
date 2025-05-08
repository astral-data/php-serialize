<?php

declare(strict_types=1);

namespace Astral\Serialize\OpenApi\Storage\OpenAPI\Method;

class POST extends Method implements MethodInterface
{
    public function getName(): string
    {
        return strtolower(str_replace("April\ApiDoc\Storage\OpenAPI\Method\\", '', __CLASS__));
    }
}
