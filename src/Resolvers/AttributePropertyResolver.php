<?php

namespace Astral\Serialize\Resolvers;

use Astral\Serialize\Contracts\Attribute\AttributePropertyCastResolver;
use Astral\Serialize\Exceptions\NotFoundAttributePropertyResolver;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Config\ConfigManager;
use InvalidArgumentException;
use ReflectionProperty;

class AttributePropertyResolver
{
    public function __construct(
        private readonly ConfigManager $configManager
    ) {

    }

    /**
     * @throws NotFoundAttributePropertyResolver
     */
    public function resolve(ReflectionProperty $property, DataCollection $dataCollection): void
    {
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
     * @throws NotFoundAttributePropertyResolver
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

        if ($this->isAttributePropertyCastResolver($cast)) {
            $cast->resolve($dataCollection);
            return;
        }

        if ($this->isAstralAttributePropertyResolver($cast)) {
            $this->configManager
                ->matchAttributePropertyResolver($cast)
                ->resolve($cast, $dataCollection);
            return;
        }

        throw new NotFoundAttributePropertyResolver(sprintf(
            'Unable to resolve cast of type %s.',
            get_class($cast)
        ));
    }

    /**
     * Check if the cast is a subclass of AttributePropertyCastResolver.
     */
    private function isAttributePropertyCastResolver(object $cast): bool
    {
        return is_subclass_of($cast, AttributePropertyCastResolver::class);
    }

    /**
     * Check if the cast is a subclass of Astral AttributePropertyResolver.
     */
    private function isAstralAttributePropertyResolver(object $cast): bool
    {
        return is_subclass_of($cast, \Astral\Serialize\Contracts\Attribute\AttributePropertyResolver::class);
    }
}
