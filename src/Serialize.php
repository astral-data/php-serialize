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
    private ?Context $_context = null;

    /**
     */
    protected function getContext(): Context
    {
        return $this->_context ??= ContextFactory::build(static::class, $this);
    }

    public function __call($name, $args)
    {
        $this->getContext()->{$name}(...$args);
        return $this;
    }

    public static function __callStatic($name, $args)
    {
        $instance  = new static(); // 创建当前类的实例
        $instance->getContext()->{$name}(...$args);

        return $instance;
    }

    public function __debugInfo()
    {
        $res             = get_object_vars($this);
        $res['_context'] = null;

        return $res;
    }
}
