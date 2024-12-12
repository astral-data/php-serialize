<?php

declare(strict_types=1);

namespace Astral\Serialize\Annotations;

use Astral\Serialize\Contracts\Attribute\AttributePropertyResolver;
use Attribute;

/**
 * toArray输出的属性名称
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class OutName implements AttributePropertyResolver
{
    public string $name;

    public array $groups;

    public function __construct(string $name, array|string $groups = [])
    {

        $this->name   = $name;
        $this->groups = is_string($groups) ? (array)$groups : $groups;
    }
}
