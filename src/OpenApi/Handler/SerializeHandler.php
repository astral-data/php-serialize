<?php

declare(strict_types=1);

namespace Astral\Serialize\OpenApi\Handler;

use Astral\Serialize\OpenApi\Annotations\Headers;
use Astral\Serialize\OpenApi\Annotations\RequestBody;
use Astral\Serialize\OpenApi\Annotations\Response;
use Astral\Serialize\OpenApi\Annotations\Route;
use Astral\Serialize\OpenApi\Annotations\Summary;
use Astral\Serialize\OpenApi\Annotations\Tag;
use Astral\Serialize\OpenApi\Collections\OpenApiCollection;
use Astral\Serialize\OpenApi\Collections\ParameterCollection;
use Astral\Serialize\OpenApi\Enum\ContentTypeEnum;
use Astral\Serialize\OpenApi\Storage\OpenAPI\Method\Method;
use Astral\Serialize\OpenApi\Storage\OpenAPI\Method\MethodInterface;
use Astral\Serialize\OpenApi\Storage\OpenAPI\RequestBodyStorage;
use Astral\Serialize\OpenApi\Storage\OpenAPI\RequestBodyStorage as OpenAPIRequestBody;
use Astral\Serialize\OpenApi\Storage\OpenAPI\ResponseStorage as OpenAPIResponse;
use Astral\Serialize\OpenApi\Storage\OpenAPI\SchemaStorage;
use Astral\Serialize\Serialize;
use Astral\Serialize\Support\Context\ChoosePropertyContext;
use Astral\Serialize\Support\Context\SerializeContext;
use Astral\Serialize\Support\Factories\ContextFactory;
use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionType;
use RuntimeException;

class SerializeHandler extends Handler
{
    /**
     * 构建OpenApi结构文档
     *
     * @param class-string $className
     * @throws ReflectionException
     * @throws Exception
     */
    public function createOpenAPIByClass(string $className): void
    {
        $classRefection = new ReflectionClass($className);
        $tagDoc         = $classRefection->getAttributes(Tag::class);
        /** @var Tag $tagDoc */
        $tagDoc = isset($tagDoc[0]) ? $tagDoc[0]->newInstance() : null;
        if ($tagDoc) {
            self::$OpenAPI->addTag($tagDoc->buildTagStorage());
        }

        foreach ($classRefection->getMethods() as $item) {

            $methodAttributes = $item->getAttributes();

            if (! $methodAttributes) {
                continue;
            }

            $routeDoc = $summaryDoc = $requestBodyDoc = $responseDoc = $headersDoc = null;
            $instances = [
                Route::class       => &$routeDoc,
                Summary::class     => &$summaryDoc,
                RequestBody::class => &$requestBodyDoc,
                Response::class    => &$responseDoc,
                Headers::class     => &$headersDoc,
            ];
            foreach ($methodAttributes as $methodAttribute) {
                $name = $methodAttribute->getName();
                if (isset($instances[$name])) {
                    $instances[$name] = $methodAttribute->newInstance();
                }
            }

            if (! $routeDoc || ! $summaryDoc) {
                continue;
            }

            $returnClass = $classRefection->getMethod($item->name)->getReturnType();
            $responseClass =  match(true){
                $responseDoc => $responseDoc->className,
                $returnClass && $returnClass instanceof Serialize::class =>$returnClass,
                default => null,
            };

             new OpenApiCollection(
                 controllerClass: $className,
                 methodName: $item->getName(),
                 summary:$summaryDoc,
                 route: $routeDoc,
                 headers: $headersDoc,
                 attributes: $methodAttributes,
                 requestBody:  $this->buildRequestBodyParameterCollections($requestBodyDoc->className),
                 response: $requestBodyDoc ? $this->buildRequestBodyParameterCollections($responseClass) : [],
            );
        }
    }

    /**
     * @param string $className
     * @return array<string, ParameterCollection>
     */
    public function buildRequestBodyParameterCollections(string $className): array
    {
        $serializeContext =  ContextFactory::build($className);
        $serializeContext->from();
        $properties = $serializeContext->getChooseSerializeContext()->getProperties();

        $vols = [];
        foreach ($properties as $property){
            $vol = new ParameterCollection(
                name:$property->getInputName(),
                descriptions: '',
                type: $property->getType()?->kind->name ?: 'STRING',
                required: !$property->getDataCollection()->isNullable(),
            );

            if($property->getChildren()){
                $vol->children = $this->buildRequestBodyParameterCollections($property->getChildren()->get)
            }
        }

        return $vols;
    }

    /**
     * @param string $className
     * @return array<string, ParameterCollection>
     */
    public function buildResponseParameterCollections(string $className): array
    {
        $serializeContext =  ContextFactory::build($className);
        $serializeContext->from();
        $properties = $serializeContext->getChooseSerializeContext()->getProperties();

        $vols = [];
        foreach ($properties as $property){

            if(!$property->getInputName()){
                continue;
            }

            $vols[] =  new  ParameterCollection(
                name:$property->getInputName(),
                descriptions: '',
                type: $property->getType()?->kind->name ?: 'STRING',
                required: !$property->getDataCollection()->isNullable(),
            );
        }

        return $vols;
    }
}
