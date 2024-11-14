<?php

declare(strict_types=1);

namespace Astral\Serialize\Annotations;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class PropertyAlisa
{
    public string $name;

    public array $group;

    public function __construct(string $name, array $group = [])
    {
        $this->name = $name;

        $this->group = $group;
    }
}
