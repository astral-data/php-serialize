<?php

namespace Astral\Serialize\Annotations\Faker;

use Astral\Serialize\Support\Context\FakerValueContext;
use Astral\Serialize\Contracts\Attribute\FakerCastInterface;
use Astral\Serialize\Support\Collections\DataCollection;
use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class FakerAction implements FakerCastInterface
{
    public function __construct(
        public string $class,
        public string $action,
        mixed ...$params
    ) {
    }

    public function resolve(DataCollection $collection): mixed
    {
        //
    }
}
