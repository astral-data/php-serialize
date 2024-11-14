<?php

declare(strict_types=1);

namespace Astral\Serialize\Annotations;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class InputIgnore
{
    public array $groups;

    public function __construct(array|string $groups = [])
    {
        $this->groups = $groups;
    }
}
