<?php

declare(strict_types=1);

namespace Astral\Serialize\OpenApi\Storage\OpenAPI;

use Astral\Serialize\OpenApi\Annotations\Parameter;
use Astral\Serialize\OpenApi\Handler\ParserPartaker;
use Astral\Serialize\OpenApi\Storage\StorageInterface;
use Astral\Serialize\OpenApi\Storage\TreeNode;
use Exception;
use ReflectionClass;
use ReflectionProperty;

/**
 * 文档开发者介绍
 */
class SchemaStorage implements StorageInterface
{
    private array|SchemaStorage $data = [
        'type'       => 'object',
        'properties' => [],
        // 'required' => [],
    ];

    public function getData(): SchemaStorage|array
    {
        return $this->data;
    }

    /**
     * Undocumented function
     *
     * @param array<int,TreeNode> $tree
     * @param mixed|null $node
     * @return void
     * @throws Exception
     */
    public function createTree(array $tree, mixed &$node = null): void
    {
        if ($node === null) {
            $node = &$this->data;
        }

        foreach ($tree as $item) {

            $parameter = $this->parserParameter($item->getValue());
            if ($parameter->ignore) {
                continue;
            }

            $node['properties'][$parameter->name] = [
                'type'        => $parameter->type,
                'description' => $parameter->value,
                'example'     => $parameter->example,
            ];

            if ($parameter->required) {
                $node['required'][] = $parameter->name;
            }

            if ($item->getChildren()) {
                // list对象
                if ($parameter->type === 'array') {
                    $node['properties'][$parameter->name]['items'] = [
                        'type'       => 'object',
                        'properties' => [],
                        // 'required' => [],
                    ];
                    $tree = &$node['properties'][$parameter->name]['items'];
                }
                // 单个对象
                elseif ($parameter->type === 'object') {
                    $node['properties'][$parameter->name] = [
                        'type'       => 'object',
                        'properties' => [],
                        // 'required' => [],
                    ];
                    $tree = &$node['properties'][$parameter->name];
                }

                $this->createTree($item->getChildren(), $tree);
            }
        }
    }

    public function addProperties(string $name, string $type, string $description, string $example, bool $required = false): void
    {
        $this->data['properties'][$name] = [
            'type'        => $type,
            'description' => $description,
            'example'     => $example,
        ];

        if ($required) {
            $this->data['required'][] = $name;
        }
    }

    private function parserParameter(ReflectionProperty $prop): Parameter
    {

        try {

            $parameterAttributes = $prop->getAttributes(Parameter::class);

            /** @var Parameter $parameter */
            $parameter = isset($parameterAttributes[0]) ? $parameterAttributes[0]->newInstance() : new Parameter();

            // 转换输入名称
            if (class_exists(GetAlias::class) && $prop->getAttributes(GetAlias::class)) {
                $getAsName       = $prop->getAttributes(GetAlias::class)[0]->newInstance();
                $parameter->name = $getAsName->name;
            } elseif (class_exists(SetAlisa::class) && $prop->getAttributes(SetAlisa::class)) {
                $setAsName       = $prop->getAttributes(SetAlisa::class)[0]->newInstance();
                $parameter->name = $setAsName->name;
            } else {
                $parameter->name = $prop->name;
            }

            $docComment = $prop->getDocComment();

            if (! $docComment) {
                return $parameter;
            }

            // 获取 类型/说明 @var {type} {name} or @var {type}
            $varMatch = getVarByDocComment($docComment);

            // 获取 例值 @example {value}
            preg_match('/@example\s*(\S+)/', $docComment, $exampleMatch);

            // 获取 是否必填 @requires true / @required true
            preg_match('/@(?:requires|required)\s*(\S+)/i', $docComment, $requiredMatch);

            if ($requiredMatch) {
                $parameter->required = $requiredMatch[1] == 'true' ? true : false;
            } elseif (class_exists(NotBlank::class) && ($prop->getAttributes(NotBlank::class) ?? [])) {
                $parameter->required = true;
            }

            if ($varMatch) {
                $parameter->type  = $this->getType($varMatch[1]);
                $parameter->value = $varMatch[2] ?? '';

                $className = str_replace(['[', ']'], '', $varMatch[1]);
                if (($parameter->type == 'object' || $parameter->type == 'array')
                    && ! in_array($className, ['int', 'float', 'bool', 'array', 'string', 'boolean', 'integer'])
                ) {
                    try {
                        $fullClassName = (new ParserPartaker())->getFullClassName($prop, $className);
                    } catch (\Throwable $th) {
                        throw new Exception(
                            sprintf('%s not find from %s', $className, $prop->getDeclaringClass()->getName()),
                            $th->getCode(),
                            $th
                        );
                    }
                    $reflectionClass = new ReflectionClass($fullClassName);
                    if ($reflectionClass->isEnum()) {
                        // object转string
                        $parameter->type = $parameter->type == 'object' ? 'string' : $parameter->type;
                        foreach ($reflectionClass->getConstants() as $enum) {
                            $parameter->value .= isset($enum->name) ? ' ' . $enum->name . ',' : '';
                        }
                        $parameter->value = rtrim($parameter->value, ',');
                    }
                }
            }
            // 获取忽略
            if (str_contains($docComment, '@ignore')) {
                $parameter->ignore = true;
            }

            if ($exampleMatch) {
                $parameter->example = $exampleMatch[1];
                if (strpos($exampleMatch['1'], '[') === 0 || strpos($exampleMatch['1'], '{') === 0) {
                    $example = json_decode($exampleMatch[1], true);
                    if ($exampleMatch[1] && json_last_error()) {
                        throw new Exception('example JSON 错误 from ' . $prop->getName() . ' in ' . $prop->getDeclaringClass()->getName());
                    }
                    $parameter->example = $example;
                }
            }

            return $parameter;
        } catch (\Throwable $th) {

            echo '参数解析失败了' . PHP_EOL;
            throw new Exception($th->getMessage(), $th->getCode(), $th);
        }
    }

    private function getType(string $type): string
    {

        /**
         * 不包含一下信息的
         * var array<*,string-class>
         * var string-class[]
         * var string-class
         */
        if (
            ! preg_match('/array<\S+,(\S+)>/', $type, $arrayMatch)
            && ! preg_match('/(\S+)\[\]/', $type, $arrayMatch)
            && ! preg_match('/(\S+)/', $type, $arrayMatch)
        ) {
            return 'string';
        }

        // 取出对应值
        $stringClass = strtolower(trim($arrayMatch[1]));

        if (stripos($type, '[]') !== false) {
            return 'array';
        }

        if (in_array($stringClass, ['int', 'float', 'bool', 'array', 'string', 'boolean', 'integer'])) {

            $stringClass = $stringClass == 'array' ? 'string' : $stringClass;
            $stringClass = $stringClass == 'int' ? 'integer' : $stringClass;
            $stringClass = $stringClass == 'bool' ? 'boolean' : $stringClass;

            return $stringClass;
        }

        if (stripos($type, '<') === false && stripos($type, '[') === false) {
            return 'object';
        }

        return 'array';
    }
}
