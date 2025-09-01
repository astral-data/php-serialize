<?php

namespace Astral\Serialize\Resolvers\Casts;

use Astral\Serialize\Support\Config\ConfigManager;

class OutNormalizerCastResolver
{
    public function __construct(
        private readonly ConfigManager $configManager
    ) {

    }

    public function resolve(mixed $values): mixed
    {
        foreach ($this->configManager->getOutNormalizerCasts() as $cast) {
            $values =  $cast->resolve($values);
        }

        return $values;
    }
}
