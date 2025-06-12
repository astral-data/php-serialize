<?php

declare(strict_types=1);

namespace Astral\Serialize\OpenApi;

use Astral\Serialize\OpenApi\Annotations\Headers;
use Astral\Serialize\OpenApi\Annotations\RequestBody;
use Astral\Serialize\OpenApi\Annotations\Response;
use Astral\Serialize\OpenApi\Annotations\Route;
use Astral\Serialize\OpenApi\Annotations\Summary;
use Astral\Serialize\OpenApi\Annotations\Tag;
use Astral\Serialize\OpenApi\Collections\OpenApiCollection;
use Astral\Serialize\OpenApi\Handler\Handler;
use Astral\Serialize\OpenApi\Storage\OpenAPI\TagStorage;
use Exception;
use ReflectionClass;
use ReflectionException;

class OpenApi extends Handler
{
    /**
     * 构建OpenApi结构文档
     *
     * @param class-string $className
     * @throws ReflectionException
     * @throws Exception
     */
    public function buildByClass(string $className): void
    {
        $classRefection = new ReflectionClass($className);
        $tagDoc         = $classRefection->getAttributes(Tag::class);
        /** @var Tag $tagDoc */
        $tagDoc = isset($tagDoc[0]) ? $tagDoc[0]->newInstance() : null;

        if ($tagDoc) {
            self::$OpenAPI->addTag(new TagStorage($tagDoc->value, $tagDoc->description));
        }

        foreach ($classRefection->getMethods() as $item) {

            $methodAttributes = $item->getAttributes();

            if (! $methodAttributes) {
                continue;
            }

            $instances = [
                Route::class       => null,
                Summary::class     => null,
                RequestBody::class => null,
                Response::class    => null,
                Headers::class     => null,
            ];

            foreach ($methodAttributes as $methodAttribute) {
                $name = $methodAttribute->getName();
                if (array_key_exists($name,$instances)) {
                    $instances[$name] = $methodAttribute->newInstance();
                }
            }

            if ($instances[Route::class] === null || $instances[Summary::class] === null) {
                continue;
            }

            $openApiCollection =  new OpenApiCollection(
                controllerClass: $className,
                methodName: $item->getName(),
                reflectionMethod: $item,
                tag: $tagDoc,
                summary: $instances[Summary::class],
                route: $instances[Route::class],
                headers: $instances[Headers::class],
                attributes: $methodAttributes,
                requestBody: $instances[RequestBody::class],
                response: $instances[Response::class],
            );

            self::$OpenAPI->addPath($openApiCollection);
        }
    }
}
