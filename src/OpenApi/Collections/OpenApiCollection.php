<?php

namespace Astral\Serialize\OpenApi\Collections;

use Astral\Serialize\Exceptions\NotFoundGroupException;
use Astral\Serialize\OpenApi\Annotations\Headers;
use Astral\Serialize\OpenApi\Annotations\OpenApi;
use Astral\Serialize\OpenApi\Annotations\RequestBody;
use Astral\Serialize\OpenApi\Annotations\Response;
use Astral\Serialize\OpenApi\Annotations\Route;
use Astral\Serialize\OpenApi\Annotations\Summary;
use Astral\Serialize\OpenApi\Annotations\Tag;
use Astral\Serialize\OpenApi\Enum\ContentTypeEnum;
use Astral\Serialize\OpenApi\Enum\ParameterTypeEnum;
use Astral\Serialize\OpenApi\Handler\Config;
use Astral\Serialize\OpenApi\Storage\OpenAPI\Method\Method;
use Astral\Serialize\OpenApi\Storage\OpenAPI\RequestBodyStorage;
use Astral\Serialize\OpenApi\Storage\OpenAPI\ResponseStorage;
use Astral\Serialize\OpenApi\Storage\OpenAPI\SchemaStorage;
use Astral\Serialize\Resolvers\GroupResolver;
use Astral\Serialize\Serialize;
use Astral\Serialize\Support\Factories\ContextFactory;
use Psr\SimpleCache\InvalidArgumentException;
use ReflectionMethod;
use ReflectionNamedType;

class OpenApiCollection
{
    public function __construct(
        public string $controllerClass,
        public string $methodName,
        public reflectionMethod $reflectionMethod,
        public Tag $tag,
        public Summary|null $summary,
        public Route|null $route,
        public Headers|null $headers,
        public array $attributes,
        public RequestBody|null $requestBody,
        public Response|null $response,
        public GroupResolver $groupResolver,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function build(): Method
    {
        $methodClass = $this->route->method->value;
        /** @var Method $openAPIMethod */
        $openAPIMethod = new $methodClass(
            tags:[$this->tag->value ?: ''],
            summary:$this->summary->value,
            description:$this->summary->description ?: ''
        );

        $requestBody = $this->buildRequestBody(
            className:$this->getRequestBodyClass(),
            contentType:$this->requestBody->contentType ?? ContentTypeEnum::JSON,
            groups: $this->requestBody->groups          ?? []
        );

        $response = $this->buildResponse(
            className:$this->getResponseClass(),
            groups:$this->response->groups ?? []
        );

        $openAPIMethod->withRequestBody($requestBody);
        $openAPIMethod->addResponse(200, $response);
        return $openAPIMethod;
    }

    public function getRequestBodyClass(): string
    {
        if ($this->requestBody?->className) {
            return $this->requestBody->className;
        }

        $methodParam        = $this->reflectionMethod->getParameters()[0] ?? null;
        $type               = $methodParam?->getType();
        $requestBodyClass   = $type instanceof ReflectionNamedType ? $type->getName() : '';
        if (is_subclass_of($requestBodyClass, Serialize::class)) {
            return $requestBodyClass;
        }

        return '';
    }

    public function buildRequestBody(string $className, ContentTypeEnum $contentType, array $groups = []): RequestBodyStorage
    {
        $openAPIRequestBody = new RequestBodyStorage($contentType);
        if (is_subclass_of($className, Serialize::class)) {
            $schemaStorage = (new SchemaStorage())->build($this->buildRequestParameterCollections($className, $groups));
            $openAPIRequestBody->withParameter($schemaStorage);
        }

        return $openAPIRequestBody;
    }

    public function getResponseClass(): string
    {
        if ($this->response?->className) {
            return $this->response->className;
        }

        $returnClass   = $this->reflectionMethod->getReturnType();
        $returnClass   = $returnClass instanceof ReflectionNamedType ? $returnClass->getName() : null;
        if (is_subclass_of($returnClass, Serialize::class)) {
            return $returnClass;
        }

        return '';
    }

    /**
     * @throws InvalidArgumentException
     */
    public function buildResponse(string $className, array $groups = []): ResponseStorage
    {
        $responseStorage = new ResponseStorage();

        $baseResponse = Config::get('response', []);

        if ($className) {
            $schemaStorage = (new SchemaStorage())->build($this->buildResponseParameterCollections($className, $groups));
            $responseStorage->withParameter($schemaStorage);
        }

        if ($baseResponse) {
            $responseStorage->addGlobParameters($baseResponse);
        }

        return $responseStorage;
    }

    /**
     * @param string $className
     * @param array $groups
     * @return array<ParameterCollection>
     * @throws InvalidArgumentException|NotFoundGroupException
     */
    public function buildRequestParameterCollections(string $className, array $groups = []): array
    {

        $serializeContext =  ContextFactory::build($className);
        $serializeContext->setGroups($groups)->from();
        $properties = $serializeContext->getGroupCollection()->getProperties();
        $groups     = $groups ?: [$className];

        $vols = [];
        foreach ($properties as $property) {


            if ($property->isInputIgnoreByGroups($groups) || !$this->groupResolver->resolveExistsGroupsByDataCollection($property, $groups, $className)) {
                continue;
            }

            $vol = new ParameterCollection(
                className: $className,
                name: current($property->getInputNamesByGroups($groups, $className)),
                types: $property->getTypes(),
                type: ParameterTypeEnum::getByTypes($property->getTypes()),
                openApiAnnotation: $this->getOpenApiAnnotation($property->getAttributes()),
                required: !$property->isNullable(),
                ignore: false,
            );

            if ($property->getChildren()) {
                foreach ($property->getChildren() as $children) {
                    $className                 = $children->getClassName();
                    $vol->children[$className] = $this->buildRequestParameterCollections($className);
                }
            }

            $vols[] = $vol;
        }

        return $vols;
    }

    /**
     * @param string $className
     * @param array $groups
     * @return array<ParameterCollection>
     * @throws InvalidArgumentException
     */
    public function buildResponseParameterCollections(string $className, array $groups = []): array
    {
        $serializeContext =  ContextFactory::build($className);
        $serializeContext->from();
        $properties = $serializeContext->getGroupCollection()->getProperties();
        $groups     = $groups ?: [$className];

        $vols = [];
        foreach ($properties as $property) {

            if ($property->isOutIgnoreByGroups($groups) || !$this->groupResolver->resolveExistsGroupsByDataCollection($property, $groups, $className)) {
                continue;
            }

            $vol = new ParameterCollection(
                className: $className,
                name: current($property->getOutNamesByGroups($groups, $className)),
                types: $property->getTypes(),
                type: ParameterTypeEnum::getByTypes($property->getTypes()),
                openApiAnnotation: $this->getOpenApiAnnotation($property->getAttributes()),
                required: !$property->isNullable(),
                ignore: false,
            );

            if ($property->getChildren()) {
                foreach ($property->getChildren() as $children) {
                    $className                 = $children->getClassName();
                    $vol->children[$className] = $this->buildResponseParameterCollections($className);
                }
            }

            $vols[] = $vol;
        }

        return $vols;
    }

    public function getOpenApiAnnotation(array $attributes): OpenApi|null
    {
        foreach ($attributes as $attribute) {
            if ($attribute->getName() === OpenApi::class) {
                return $attribute->newInstance();
            }
        }

        return null;
    }
}
