<?php

namespace Astral\Serialize;

use ReflectionException;
use Astral\Serialize\Exceptions\NotFoundGroupException;
use Astral\Serialize\Support\Context\SerializeContext;
use Astral\Serialize\Support\Factories\ContextFactory;
use Psr\SimpleCache\InvalidArgumentException;

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
     * @param array<string> $groups
     * @return SerializeContext|null
     */
    public static function setGroups(array $groups): ?SerializeContext
    {
        /** @var SerializeContext<static> $serializeContext */
        $serializeContext = ContextFactory::build(static::class);
        return $serializeContext->setGroups($groups);

    }


    public function toArray(): array
    {
        try {
            return $this->getContext()->toArray($this);
        } catch (InvalidArgumentException $e) {

        }

        return [];
    }


    public static function from(...$payload): static
    {
        $serializeContext = ContextFactory::build(static::class);

        /** @var static $instance */
        $instance = $serializeContext->from(...$payload);
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
