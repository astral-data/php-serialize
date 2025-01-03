<?php

namespace Astral\Serialize\Resolvers;

use Astral\Serialize\Exceptions\NotFoundAttributePropertyResolver;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Collections\GroupDataCollection;
use Astral\Serialize\Support\Context\ChooseSerializeContext;
use Astral\Serialize\Support\Context\OutContext;
use Astral\Serialize\Support\Instance\ReflectionClassInstanceManager;
use ReflectionException;

class PropertyToArrayResolver
{
    public function __construct(
        protected readonly ReflectionClassInstanceManager $reflectionClassInstanceManager,
        private readonly OutValueCastResolver $outValueCastResolver,
        protected readonly GroupResolver $groupResolver,
    ) {

    }

    /**
     * @throws NotFoundAttributePropertyResolver
     * @throws ReflectionException
     */
    public function resolve(ChooseSerializeContext $chooseContext, GroupDataCollection $groupCollection, object $object): array
    {

        $reflectionClass =  $this->reflectionClassInstanceManager->get($groupCollection->getClassName());
        $object          = $reflectionClass->newInstanceWithoutConstructor();

        $context         = new OutContext(
            className: $groupCollection->getClassName(),
            propertyToArrayResolver: $this,
            chooseSerializeContext: $chooseContext,
        );

        $properties = $groupCollection->getProperties();

        $toArray  = [];
        foreach ($properties as $collection) {

            $name = $collection->getName();

            $matchInput = $this->matchInputNameAndValue($chooseContext, $collection, $object);

            if ($matchInput === false) {
                continue;
            }

            $chooseContext->getProperty($name)->setInputName($matchInput['names']);

            $resolvedValue = $matchInput['value'];

            $resolvedValue = $this->outValueCastResolver->resolve(
                value:$resolvedValue,
                collection:$collection,
                context: $context,
            );

            foreach ($matchInput['names'] as $name) {
                $toArray[$name] = $resolvedValue;
            }

        }

        return $toArray;

    }

    public function matchInputNameAndValue(ChooseSerializeContext $chooseContext, DataCollection $collection, object $object): array|false
    {

        $defaultGroup = $chooseContext->serializeClass;
        $groups       = $chooseContext->getGroups();
        $outNames     = $collection->getOutNamesByGroups($groups, $defaultGroup);

        return !$this->groupResolver->resolveExistsGroupsByDataCollection($collection, $groups, $defaultGroup) || $collection->isOutIgnoreByGroups($groups)
            ? false : ['names' => $outNames ,'value' => $object->{$collection->getName()}];
    }
}
