<?php

declare(strict_types=1);

namespace Astral\Serialize\Casts;

use ReflectionClass;
use ReflectionException;

class InputConstructCast_bak
{

    /**
     * @throws ReflectionException
     */
    public function resolve(ReflectionClass $reflectionClass, array &$values): object
    {
        $constructor = $reflectionClass->getConstructor();
        $params = $constructor->getParameters();

        $args = [];
        foreach ($params as $param) {
            $name = $param->getName();
            if (array_key_exists($name, $values)) {
                $args[] = $values[$name];
                unset($values[$name]); // 从 $values 中移除
            } elseif ($param->isOptional()) {
                // 如果参数有默认值，使用默认值
                $args[] = $param->getDefaultValue();
            } else {
                throw new InvalidArgumentException("Missing required parameter: $name");
            }
        }

        $object = $reflectionClass->newInstance($args);
        foreach ($values as $key => $value) {
            $property = $reflectionClass->getProperty($key);
            $property->setValue($object, $value);
        }

        return $object;
    }


}
