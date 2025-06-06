<?php

declare(strict_types=1);

namespace Astral\Serialize\OpenApi\Storage\OpenAPI\Method;

use Astral\Serialize\OpenApi\Storage\OpenAPI\RequestBodyStorage;
use Astral\Serialize\OpenApi\Storage\OpenAPI\ResponseStorage;
use stdClass;

class Method
{

    public function __construct(
        public array              $tags = [],
        public string             $summary = '',
        public string             $description = '',
        /** @var array $parameters */
        public array $parameters = [],
        public array|stdClass $requestBody = new stdClass(),
        /** @var array<string,ResponseStorage>  $responses */
        public array|stdClass $responses = new stdClass(),
    ) {
    }

    public function withTags(array $tags): Method
    {
        $this->tags = $tags;

        return $this;
    }

    public function withParameters(array $parameters): static
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function addParameters(array $parameters): void
    {
        $this->parameters[] = $parameters;
    }

    /**
     * @return $this
     */
    public function withRequestBody(RequestBodyStorage $body): static
    {
        $this->requestBody = $body->getData();
        return $this;
    }

    /**
     * @param int $code
     * @param ResponseStorage $response
     * @return $this
     */
    public function addResponse(int $code, ResponseStorage $response): static
    {

        $this->responses        = [];
        $this->responses[$code] = $response->getData();

        return $this;
    }
}
