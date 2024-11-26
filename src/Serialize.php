<?php

namespace Astral\Serialize;

use Astral\Serialize\Support\Factories\ContextFactory;
use Astral\Serialize\Support\Instance\SerializeInstanceManager;

abstract class Serialize
{
    public static function from(mixed $payload, array $groups = []): static
    {
        /** @var static $instance */
        $instance = SerializeInstanceManager::get(static::class);
        $instance->getContext($groups)->setPayload($payload);

        return $instance;
    }

    public function toArray()
    {
    }

    protected function getContext(array $groups = []): Context
    {
        return ContextFactory::build(static::class, $groups);
    }
}
