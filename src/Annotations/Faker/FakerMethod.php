<?php

namespace Astral\Serialize\Annotations\Faker;

use Astral\Serialize\Contracts\Attribute\FakerCastInterface;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Factories\ContextFactory;
use Attribute;
use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use RuntimeException;

#[Attribute(Attribute::TARGET_PROPERTY)]
class FakerMethod implements FakerCastInterface
{
    public function __construct(
        /** @var class-string $className  */
        public string $className,
        public string $methodName,
        public ?array $params = [],
        public ?string $returnType = null
    ) {
    }

    /**
     * @throws Exception
     */
    public function resolve(DataCollection $collection): mixed
    {
        if (!class_exists($this->className)) {
            throw new RuntimeException("Class $this->className not found");
        }

        if (!method_exists($this->className, $this->methodName)) {
            throw new RuntimeException("Method $this->methodName not found from class $this->className");
        }

        $instance         = $this->createInstanceWithResolvedConstructor($this->className);
        $reflectionMethod = new ReflectionMethod($this->className, $this->methodName);
        $resolvedParams   = $this->resolveMethodParameters($reflectionMethod);

        $result = $reflectionMethod->invokeArgs($instance, $resolvedParams);

        if ($this->returnType) {
            return $this->extractNestedValue($result, $this->returnType);
        }

        return $result;

    }

    /**
     * Creates an instance of the class, resolving its constructor parameters if necessary.
     * Handles nested constructors by resolving dependencies recursively.
     *
     * @param string $className
     * @param array $dependencyChain
     * @return object
     * @throws ReflectionException
     * @throws Exception
     */
    private function createInstanceWithResolvedConstructor(string $className, array &$dependencyChain = []): object
    {
        if (in_array($className, $dependencyChain, true)) {
            $chain = implode(' -> ', array_merge($dependencyChain, [$className]));
            throw new RuntimeException("Circular dependency detected: $chain");
        }

        $dependencyChain[] = $className;
        $reflectionClass   = new ReflectionClass($className);

        if (!$reflectionClass->hasMethod('__construct')) {
            return $reflectionClass->newInstance();
        }

        $constructor = $reflectionClass->getConstructor();

        return $reflectionClass->newInstanceArgs(array_map(function ($param) use ($dependencyChain) {
            $paramType = $param->getType();

            if(!$paramType instanceof ReflectionNamedType){
                throw new \http\Exception\RuntimeException("$paramType is not ReflectionNamedType");
            }

            $typeName  = $paramType->getName();

            return match(true) {
                !$paramType->isBuiltin() && class_exists($typeName) => $this->createInstanceWithResolvedConstructor($typeName, $dependencyChain),
                $param->isDefaultValueAvailable()               => $param->getDefaultValue(),
                default                                         => null,
            };

        }, $constructor?->getParameters()));
    }

    /**
     * Resolves method parameters dynamically.
     *
     * @param ReflectionMethod $method
     * @return array
     * @throws ReflectionException
     */
    private function resolveMethodParameters(ReflectionMethod $method): array
    {
        $parameters = $method->getParameters();


        return array_map(function (ReflectionParameter $param) {
            $paramType = $param->getType();

            if(!$paramType instanceof ReflectionNamedType){
                throw new \http\Exception\RuntimeException("$paramType is not ReflectionNamedType");
            }

            $name      = $param->getName();
            $typeName  = $paramType->getName();

            return match(true) {
                !$paramType->isBuiltin() && class_exists($typeName)             => ContextFactory::build($typeName)->faker(),
                array_key_exists($name, $this->params)                          => $this->params[$name],
                $param->isDefaultValueAvailable()                               => $param->getDefaultValue(),
                default                                                         => null,
            };

        }, $parameters);
    }

    /**
     * Extracts a nested value from the result based on the returnType string.
     *
     * @param mixed $result
     * @param string $path
     * @return mixed
     * @throws Exception
     */
    private function extractNestedValue(mixed $result, string $path): mixed
    {
        $keys = explode('.', $path);

        foreach ($keys as $key) {
            if (is_array($result) && array_key_exists($key, $result)) {
                $result = $result[$key];
            } elseif (is_object($result) && property_exists($result, $key)) {
                $result = $result->$key;
            } else {
                throw new RuntimeException("Unable to extract path '$path' from result");
            }
        }
        return $result;
    }
}
