<?php

namespace Astral\Serialize;

use Astral\Serialize\Support\Factories\ContextFactory;

/**
 * @method static Context setGroups(array $groups)
 * @method static Context from(...$values)
 * @method static Context toArray()
 *
 * @see Context
 */
abstract class Serialize
{
    protected ?Context $_context = null;

    /**
     */
    protected function getContext(): Context
    {
        return $this->_context ??= ContextFactory::build(static::class);
    }

    public function __call($name, $args)
    {
        $instances = $this->getContext(); // 调用实例的上下文
        $instances->{$name}(...$args);

        return $instances;
    }

    public static function __callStatic($name, $args)
    {
        $instance  = new static(); // 创建当前类的实例
        $instances = $instance->getContext(); // 获取实例的上下文
        $instances->{$name}(...$args);

        return $instances;
    }
}
