<?php

namespace Astral\Serialize\Resolvers\Casts;

use Astral\Serialize\Contracts\Attribute\OutValueCastInterface;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Config\ConfigManager;
use Astral\Serialize\Support\Context\OutContext;
use InvalidArgumentException;

class OutputCastResolver
{
    public function __construct(
        private readonly ConfigManager $configManager
    ) {

    }

    public function resolve(mixed $value, DataCollection $collection, OutContext $context): mixed
    {
        $value = $this->applyCastsByConfigManager($value, $collection, $context);

        $attributes =  $collection->getAttributes();
        if (!$attributes) {
            return $value;
        }

        foreach ($attributes as $attribute) {
            $value = $this->applyCast($attribute->newInstance(), $collection, $value, $context);
        }

        return $value;
    }

    /**
     * Resolve the cast based on its type.
     *
     * @throws InvalidArgumentException
     */
    private function applyCastsByConfigManager(mixed $value, DataCollection $collection, OutContext $context): mixed
    {
        foreach ($this->configManager->getOutValueCasts() as $cast) {
            $value = $this->applyCast($cast, $collection, $value, $context);
        }

        return $value;
    }

    /**
     * Resolve the cast based on its type.
     *
     * @throws InvalidArgumentException
     */
    private function applyCast(object $cast, DataCollection $collection, mixed $value, OutContext $context): mixed
    {
        if (!is_subclass_of($cast, OutValueCastInterface::class)) {
            return $value;
        }

        if ($cast->match($value, $collection, $context)) {
            return $cast->resolve($value, $collection, $context);
        }

        return  $value;
    }
}
