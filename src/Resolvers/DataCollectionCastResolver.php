<?php

namespace Astral\Serialize\Resolvers;

use Astral\Serialize\Contracts\Attribute\DataCollectionCastInterface;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Config\ConfigManager;
use InvalidArgumentException;

class DataCollectionCastResolver
{
    public function __construct(
        private readonly ConfigManager $configManager
    ) {

    }

    public function resolve(DataCollection $dataCollection): void
    {

        foreach ($this->configManager->getAttributePropertyResolver() as $cast) {
            $cast->resolve($dataCollection);
        }

        $attributes =  $dataCollection->getAttributes();

        if (!$attributes) {
            return;
        }

        foreach ($attributes as $attribute) {
            $this->resolveCast($attribute->newInstance(), $dataCollection);
        }
    }

    /**
     * Resolve the cast based on its type.
     *
     * @throws InvalidArgumentException
     */
    private function resolveCast(object $cast, DataCollection $dataCollection): void
    {
        if (!is_object($cast)) {
            throw new InvalidArgumentException(sprintf(
                'Expected an object, but got %s.',
                gettype($cast)
            ));
        }

        if (is_subclass_of($cast, DataCollectionCastInterface::class)) {
            $cast->resolve($dataCollection);
        }
    }
}
