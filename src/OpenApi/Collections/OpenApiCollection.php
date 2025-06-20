<?php

namespace Astral\Serialize\OpenApi\Collections;

use Astral\Serialize\OpenApi\Annotations\Headers;
use Astral\Serialize\OpenApi\Annotations\OpenApi;
use Astral\Serialize\OpenApi\Annotations\RequestBody;
use Astral\Serialize\OpenApi\Annotations\Response;
use Astral\Serialize\OpenApi\Annotations\Route;
use Astral\Serialize\OpenApi\Annotations\Summary;
use Astral\Serialize\OpenApi\Annotations\Tag;
use Astral\Serialize\OpenApi\Enum\ContentTypeEnum;
use Astral\Serialize\OpenApi\Enum\ParameterTypeEnum;
use Astral\Serialize\OpenApi\Storage\OpenAPI\Method\Method;
use Astral\Serialize\OpenApi\Storage\OpenAPI\RequestBodyStorage;
use Astral\Serialize\OpenApi\Storage\OpenAPI\ResponseStorage;
use Astral\Serialize\OpenApi\Storage\OpenAPI\SchemaStorage;
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
        public Summary $summary,
        public Route $route,
        public Headers|null $headers,
        public array $attributes,
        public RequestBody|null $requestBody,
        public Response|null $response,
    ){
    }

    /**
     * @throws InvalidArgumentException
     */
    public function build() : Method
    {
        $methodClass = $this->route->method->value;
        /** @var Method $openAPIMethod */
        $openAPIMethod = new $methodClass(
            tags:[$this->tag->value ?: ''],
            summary:$this->summary->value,
            description:$this->summary->description ?: ''
        );

        $openAPIMethod->withRequestBody($this->requestBody !== null ? $this->buildRequestBodyByAttribute() : $this->buildRequestBodyByParameters());
        $openAPIMethod->addResponse(200, $this->buildResponse());
        return $openAPIMethod;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function buildRequestBodyByAttribute(): RequestBodyStorage
    {
        $openAPIRequestBody = new RequestBodyStorage($this->requestBody->contentType);
        $schemaStorage = (new SchemaStorage())->build($this->buildParameterCollections($this->requestBody->className,$this->requestBody->groups));
        $openAPIRequestBody->withParameter($schemaStorage);
        return $openAPIRequestBody;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function buildRequestBodyByParameters(): RequestBodyStorage
    {
        $openAPIRequestBody = new RequestBodyStorage(ContentTypeEnum::JSON);
        $methodParam = $this->reflectionMethod->getParameters()[0] ?? null;
        $type = $methodParam?->getType();
        $requestBodyClass = $type instanceof ReflectionNamedType  ? $type->getName() : '';
        if (is_subclass_of($requestBodyClass, Serialize::class)) {
            $schemaStorage = (new SchemaStorage())->build($this->buildParameterCollections($requestBodyClass));
            $openAPIRequestBody->withParameter($schemaStorage);
        }

        return $openAPIRequestBody;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function buildResponse(): ResponseStorage
    {
        $returnClass = $this->reflectionMethod->getReturnType();
        $returnClass = $returnClass instanceof ReflectionNamedType ? $returnClass->getName() : null;
        $responseClass =  match(true){
            $this->response !== null => $this->response->className,
            $returnClass && is_subclass_of($returnClass,Serialize::class) => $returnClass,
            default => null,
        };

        $responseStorage = new ResponseStorage();


        if($responseClass) {
            $groups = $this->response && is_array($this->response->groups) ?  $this->response->groups : ['default'];
            $schemaStorage = (new SchemaStorage())->build($this->buildParameterCollections($responseClass, $groups));
            $responseStorage->withParameter($schemaStorage);
        }

        return $responseStorage;
    }

    /**
     * @param string $className
     * @param array $groups
     * @return array<ParameterCollection>
     * @throws InvalidArgumentException
     */
    public function buildParameterCollections(string $className, array $groups = ['default']): array
    {
        $serializeContext =  ContextFactory::build($className);
        $serializeContext->from();
        $properties = $serializeContext->getGroupCollection()->getProperties();

        $vols = [];
        foreach ($properties as $property){
            $vol = new ParameterCollection(
                className: $className,
                name: current($property->getInputNamesByGroups($groups,$className)),
                types: $property->getTypes(),
                type: ParameterTypeEnum::getByTypes($property->getTypes()),
                openApiAnnotation: $this->getOpenApiAnnotation($property->getAttributes()),
                required: !$property->isNullable(),
                ignore: false,
            );

            if($property->getChildren()){
                foreach ($property->getChildren() as $children){
                    $className = $children->getClassName();
                    $vol->children[$className] = $this->buildParameterCollections($className);
                }
            }

            $vols[] = $vol;
        }

        return $vols;
    }

    public function getOpenApiAnnotation(array $attributes): OpenApi|null
    {
        foreach ($attributes as $attribute){
            if($attribute->getName() === OpenApi::class){
               return $attribute->newInstance();
            }
        }

        return null;
    }
}