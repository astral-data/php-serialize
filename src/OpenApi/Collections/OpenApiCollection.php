<?php

namespace Astral\Serialize\OpenApi\Collections;

use Astral\Serialize\OpenApi\Annotations\Headers;
use Astral\Serialize\OpenApi\Annotations\Route;
use Astral\Serialize\OpenApi\Annotations\Summary;

class OpenApiCollection
{
    public function __construct(
        public string $controllerClass,
        public string $methodName,
        public Summary $summary,
        public Route $route,
        public Headers $headers,
        public array $attributes,
        /** @var array<string, ParameterCollection> $parameters, */
        public array $parameters = [],
        /** @var array<string, ParameterCollection|mixed> $requestBody, */
        public array $requestBody = [],
        /** @var array<string, ParameterCollection|mixed> $responses, */
        public array $response = [],

    ){
    }

}