<?php

namespace Astral\Serialize\Faker;

use Astral\Serialize\Casts\InputConstructCast;
use Astral\Serialize\Faker\Rule\FakerDefaultRules;
use Astral\Serialize\Resolvers\GroupResolver;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Collections\GroupDataCollection;
use Astral\Serialize\Support\Context\ChooseSerializeContext;
use Astral\Serialize\Support\Instance\ReflectionClassInstanceManager;

class FakerResolver
{
    public function __construct(
        protected readonly ReflectionClassInstanceManager $reflectionClassInstanceManager,
        private readonly FakerDefaultRules $fakerDefaultRules,
        protected readonly FakerCastResolver $fakerCastResolver,
        protected readonly InputConstructCast $inputConstructCast,
        protected readonly GroupResolver $groupResolver,
    ) {

    }

    public function resolve(ChooseSerializeContext $chooseContext, GroupDataCollection $groupCollection)
    {

        $reflectionClass =  $this->reflectionClassInstanceManager->get($groupCollection->getClassName());
        $object          =  $reflectionClass->newInstanceWithoutConstructor();

        $properties = $groupCollection->getProperties();

        $constructInputs = [];
        foreach ($properties as $collection) {

            $name = $collection->getName();


            $matchName = $this->matchName($chooseContext, $collection);

            if ($matchName === false) {
                continue;
            }

            $resolvedValue = $this->fakerCastResolver->resolve($collection);

            if ($resolvedValue === null) {
                $resolvedValue = $this->fakerDefaultRules->resolve($collection->getTypes(), $collection->getName());
            }

            if ($groupCollection->hasConstruct() && $groupCollection->hasConstructProperty($name)) {
                $constructInputs[$name] = $resolvedValue;
            } else {
                $collection->getProperty()->setValue($object, $resolvedValue);
            }
        }

        if ($groupCollection->hasConstruct()) {
            $this->inputConstructCast->resolve($groupCollection->getConstructProperties(), $object, $constructInputs);
        }

        return $object;

    }

    public function matchName(ChooseSerializeContext $chooseContext, DataCollection $collection): bool
    {
        $defaultGroup = $chooseContext->serializeClass;
        $groups       = $chooseContext->getGroups();

        if ($this->isConstructProperty($collection->getParentGroupCollection(), $collection)) {
            return true;
        }

        return !(!$this->groupResolver->resolveExistsGroupsByDataCollection($collection, $groups, $defaultGroup) || $collection->isInputIgnoreByGroups($groups));

    }

    private function isConstructProperty(GroupDataCollection $groupCollection, DataCollection $collection): bool
    {
        $constructProperty = $groupCollection->getConstructProperty($collection->getName());
        return (bool)$constructProperty;
    }
}
