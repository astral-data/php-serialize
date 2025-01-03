<?php

namespace Astral\Serialize;

use Astral\Serialize\Exceptions\NotFoundAttributePropertyResolver;
use Astral\Serialize\Exceptions\NotFoundGroupException;
use Astral\Serialize\Support\Context\SerializeContext;
use Astral\Serialize\Support\Factories\ContextFactory;
use Psr\SimpleCache\InvalidArgumentException;
use ReflectionException;

/**
 * @method static SerializeContext toArray()
 *
 * @see SerializeContext
 */
abstract class Serialize
{
    private ?SerializeContext $_context = null;

    /**
     */
    public function getContext(): ?SerializeContext
    {
        return $this->_context;
    }

    public function setContext(SerializeContext $context): static
    {
        $this->_context = $context;
        return  $this;
    }

    /**
     * @throws ReflectionException
     * @throws NotFoundGroupException
     * @throws InvalidArgumentException
     */
    public static function setGroups(array $groups): SerializeContext
    {
        $serializeContext = ContextFactory::build(static::class);
        $serializeContext->setGroups($groups);

        return $serializeContext;
    }

    /**
     * @throws ReflectionException
     * @throws NotFoundAttributePropertyResolver
     * @throws NotFoundGroupException
     * @throws InvalidArgumentException
     */
    public function toArray(): array
    {
        return $this->getContext()->toArray($this);
    }

    /**
     * @throws NotFoundAttributePropertyResolver
     * @throws ReflectionException
     * @throws NotFoundGroupException
     * @throws InvalidArgumentException
     */
    public static function from(...$payload): static
    {

        $serializeContext = ContextFactory::build(static::class);
        /** @var static $instance */
        $instance =  $serializeContext->from(...$payload);
        $instance->setContext($serializeContext);

        return $instance;
    }

    public function __debugInfo()
    {
        $res             = get_object_vars($this);
        $res['_context'] = null;

        return $res;
    }
}
