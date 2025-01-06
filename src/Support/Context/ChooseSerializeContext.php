<?php

namespace Astral\Serialize\Support\Context;

/**
 * @template T
 */
class ChooseSerializeContext
{
    private array $groups;

    /** @var array<string,ChoosePropertyContext> $properties */
    private array $properties;

    public function __construct(
        /** @var class-string<T> $serializeClass */
        public readonly string $serializeClass,
    ) {

    }

    public function getGroups(): array
    {
        return $this->groups;
    }

    public function setGroups(array $groups): void
    {
        $this->groups = array_unique(array_merge([$this->serializeClass], $groups));
    }

    public function addProperty(ChoosePropertyContext $context): void
    {
        $this->properties[$context->getName()] = $context;
    }

    public function getProperty(string $name): ?ChoosePropertyContext
    {
        return $this->properties[$name] ?? null;
    }
}
