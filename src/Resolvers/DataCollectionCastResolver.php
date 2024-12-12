<?php

namespace Astral\Serialize\Resolvers;

use ReflectionProperty;
use Astral\Serialize\Contracts\Attribute\DataCollectionCastInterface;
use Astral\Serialize\Contracts\Attribute\InputValueCastInterface;
use Astral\Serialize\Exceptions\NotFoundAttributePropertyResolver;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Config\ConfigManager;
use InvalidArgumentException;

class DataCollectionCastResolver
{
    public function __construct(
        private readonly ConfigManager $configManager
    ) {

    }

    /**
     * @throws NotFoundAttributePropertyResolver
     */
    public function resolve(DataCollection $dataCollection, ReflectionProperty $property): void
    {
        $this->configManager->getAttributePropertyResolver()
            ->each(function (DataCollectionCastInterface $goalCast) use ($dataCollection) {
                $goalCast->resolve($dataCollection);
            });

        $attributes =  $dataCollection->getAttributes();

        if(!$attributes) {
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
    public function resolveCast(object $cast, DataCollection $dataCollection): void
    {
        if (!is_object($cast)) {
            throw new InvalidArgumentException(sprintf(
                'Expected an object, but got %s.',
                gettype($cast)
            ));
        }

        if(is_subclass_of($cast, DataCollectionCastInterface::class)) {
            $cast->resolve($dataCollection);
        }
    }
}
