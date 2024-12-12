<?php

declare(strict_types=1);

namespace Astral\Serialize\Annotations;

use Astral\Serialize\Contracts\Attribute\AttributePropertyResolver;
use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class OutIgnore implements AttributePropertyResolver
{
    public array $groups;

    public function __construct(array|string $groups = [])
    {
        $this->groups = $groups;
    }
}
