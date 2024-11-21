<?php

namespace Astral\Serialize;

use Astral\Serialize\Support\Instance\SerializeInstanceManager;

abstract class Serialize {

    public static function from(mixed $payload,array $groups = []): static
    {
        /** @var static $instance */
        $instance = SerializeInstanceManager::build(static::class);
        $instance->getContext()->setGroups($groups)->setPayload($payload);

        return $instance;
    }

    public function toArray()
    {

    }



    protected function getContext()
    {
//            Serialize::withGroups([])::from()
    }

}