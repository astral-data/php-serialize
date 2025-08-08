<?php

namespace Astral\Serialize\Resolvers\Casts;

use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Config\ConfigManager;
use Astral\Serialize\Support\Context\InputValueContext;

class NormalizerCastResolver
{
    public function __construct(
        private readonly ConfigManager $configManager
    ) {

    }

    public function resolve(mixed $values): mixed
    {
        foreach ($this->configManager->getNormalizerCasts() as $cast) {
            $values =  $cast->resolve($values);
        }

        return $values;
    }
}