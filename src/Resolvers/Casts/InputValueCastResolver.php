<?php

namespace Astral\Serialize\Resolvers\Casts;

use InvalidArgumentException;
use Astral\Serialize\Support\Config\ConfigManager;
use Astral\Serialize\Support\Context\InputValueContext;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Contracts\Attribute\InputValueCastInterface;

class InputValueCastResolver
{
    public function __construct(
        private readonly ConfigManager $configManager
    ) {

    }

    public function resolve(mixed $value, DataCollection $collection, InputValueContext $context): mixed
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
    private function applyCastsByConfigManager(mixed $value, DataCollection $collection, InputValueContext $context): mixed
    {
        foreach ($this->configManager->getInputValueCasts() as $cast) {
            $value = $this->applyCast($cast, $collection, $value, $context);
        }

        return $value;
    }

    /**
     * Resolve the cast based on its type.
     *
     * @throws InvalidArgumentException
     */
    private function applyCast(object $cast, DataCollection $collection, mixed $value, InputValueContext $context): mixed
    {
        if (!is_object($cast)) {
            throw new InvalidArgumentException(sprintf(
                'Expected an object, but got %s.',
                gettype($cast)
            ));
        }

        if (!is_subclass_of($cast, InputValueCastInterface::class)) {
            return $value;
        }

        if ($cast->match($value, $collection, $context)) {
            return $cast->resolve($value, $collection, $context);
        }

        return  $value;
    }
}
