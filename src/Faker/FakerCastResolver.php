<?php

namespace Astral\Serialize\Faker;

use Astral\Serialize\Contracts\Attribute\FakerCastInterface;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Config\ConfigManager;
use InvalidArgumentException;

class FakerCastResolver
{
    public function __construct(
        private readonly ConfigManager $configManager
    ) {

    }

    public function resolve(DataCollection $dataCollection): mixed
    {

        //        foreach ($this->configManager->getAttributePropertyResolver() as $cast) {
        //            $cast->resolve($dataCollection);
        //        }

        $attributes =  $dataCollection->getAttributes();

        if (!$attributes) {
            return null;
        }

        foreach ($attributes as $attribute) {
            $value = $this->resolveCast($attribute->newInstance(), $dataCollection);
        }

        return  $value ?? null;
    }

    /**
     * Resolve the cast based on its type.
     *
     * @throws InvalidArgumentException
     */
    private function resolveCast(object $cast, DataCollection $dataCollection): mixed
    {
        if (!is_object($cast)) {
            throw new InvalidArgumentException(sprintf(
                'Expected an object, but got %s.',
                gettype($cast)
            ));
        }

        if (is_subclass_of($cast, FakerCastInterface::class)) {
            return $cast->resolve($dataCollection);
        }

        return null;
    }
}
