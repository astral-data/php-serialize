<?php

namespace Astral\Serialize\OpenApi\Enum;

use Astral\Serialize\OpenApi\Storage\OpenAPI\Method\Delete;
use Astral\Serialize\OpenApi\Storage\OpenAPI\Method\Get;
use Astral\Serialize\OpenApi\Storage\OpenAPI\Method\Post;
use Astral\Serialize\OpenApi\Storage\OpenAPI\Method\Put;

enum MethodEnum:string
{
    case POST = Post::class;
    case GET = Get::class;
    case PUT = Put::class;
    case DELETE = Delete::class;

}
