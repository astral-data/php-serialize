<?php

namespace Astral\Serialize;

use Astral\Serialize\Support\Context\SerializeContext;
use Astral\Serialize\Support\Factories\ContextFactory;

abstract class Serialize
{
    private ?SerializeContext $_context = null;

    public function getContext(): ?SerializeContext
    {
        return $this->_context;
    }

    public function setContext(SerializeContext $context): static
    {
        $this->_context = $context;
        return  $this;
    }

    public static function setGroups(array|string $groups): SerializeContext
    {
        /** @var SerializeContext<static> $serializeContext */
        $serializeContext = ContextFactory::build(static::class);
        return $serializeContext->setGroups((array)$groups);
    }

    public function toArray(): array
    {
        if ($this->getContext() === null) {
            $this->setContext(ContextFactory::build(static::class));
        }

        return $this->getContext()->toArray($this);
    }

    public static function from(...$payload): static
    {
        $serializeContext = ContextFactory::build(static::class);

        /** @var static $instance */
        $instance = $serializeContext->from(...$payload);
        $instance->setContext($serializeContext);

        return $instance;
    }

    public static function faker(): static
    {
        $serializeContext = ContextFactory::build(static::class);

        /** @var static $instance */
        $instance = $serializeContext->faker();
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
