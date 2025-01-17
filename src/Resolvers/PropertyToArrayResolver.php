<?php

namespace Astral\Serialize\Resolvers;

use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Collections\GroupDataCollection;
use Astral\Serialize\Support\Context\ChooseSerializeContext;
use Astral\Serialize\Support\Context\OutContext;
use Astral\Serialize\Support\Instance\ReflectionClassInstanceManager;

class PropertyToArrayResolver
{
    public function __construct(
        protected readonly ReflectionClassInstanceManager $reflectionClassInstanceManager,
        private readonly OutValueCastResolver $outValueCastResolver,
        protected readonly GroupResolver $groupResolver,
    ) {

    }

    public function resolve(ChooseSerializeContext $chooseContext, GroupDataCollection $groupCollection, object $object): array
    {

        $context         = new OutContext(
            className: $groupCollection->getClassName(),
            classInstance: $object,
            propertyToArrayResolver: $this,
            chooseSerializeContext: $chooseContext,
        );

        $properties = $groupCollection->getProperties();

        $toArray  = [];
        foreach ($properties as $collection) {


            $matchData = $this->matchNameAndValue($chooseContext, $collection, $object);

            if ($matchData === false) {
                continue;
            }

            $resolvedValue = $matchData['value'];

            $resolvedValue = $this->outValueCastResolver->resolve(
                value:$resolvedValue,
                collection:$collection,
                context: $context,
            );

            foreach ($matchData['names'] as $name) {
                $toArray[$name] = $resolvedValue;
            }
        }

        return $toArray;

    }

    public function matchNameAndValue(ChooseSerializeContext $chooseContext, DataCollection $collection, object $object): array|false
    {

        $defaultGroup = $chooseContext->serializeClass;
        $groups       = $chooseContext->getGroups();
        $outNames     = $collection->getOutNamesByGroups($groups, $defaultGroup);

        return $this->groupResolver->resolveExistsGroupsByDataCollection($collection, $groups, $defaultGroup) && !$collection->isOutIgnoreByGroups($groups)
            ? ['names' => $outNames ,'value' => $object->{$collection->getName()} ?? null] : false;
    }
}
