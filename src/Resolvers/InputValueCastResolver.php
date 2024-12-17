<?php

namespace Astral\Serialize\Resolvers;

use Astral\Serialize\Contracts\Attribute\InputValueCastInterface;
use Astral\Serialize\Exceptions\NotFoundAttributePropertyResolver;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Config\ConfigManager;
use InvalidArgumentException;

class InputValueCastResolver
{
    public function __construct(
        private readonly ConfigManager $configManager
    ) {

    }

    /**
     * @throws NotFoundAttributePropertyResolver
     */
    public function resolve(mixed $value, DataCollection $dataCollection): mixed
    {

        foreach ($this->configManager->getAttributePropertyResolver() as $cast) {
            $cast->resolve($dataCollection);
        }

        $attributes =  $dataCollection->getAttributes();

        if (!$attributes) {
            return $value;
        }

        foreach ($attributes as $attribute) {
            $value = $this->resolveCast($attribute->newInstance(), $dataCollection, $value);
        }

        return $value;
    }

    /**
     * Resolve the cast based on its type.
     *
     * @throws InvalidArgumentException
     */
    private function resolveCast(object $cast, DataCollection $dataCollection, mixed $value): mixed
    {
        if (!is_object($cast)) {
            throw new InvalidArgumentException(sprintf(
                'Expected an object, but got %s.',
                gettype($cast)
            ));
        }

        if (is_subclass_of($cast, InputValueCastInterface::class)) {
            return  $cast->resolve($value, $dataCollection);
        }

        return  $value;
    }
}
