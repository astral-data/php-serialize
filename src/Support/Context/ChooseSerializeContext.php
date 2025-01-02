<?php

namespace Astral\Serialize\Support\Context;

class ChooseSerializeContext
{
    public array $groups;

    /** @var array<string,ChoosePropertyContext> $properties */
    public array $properties;

    public function __construct(
        public readonly string $serializeClass,
    ) {

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
