<?php

declare(strict_types=1);

namespace Astral\Serialize\Annotations;

use Astral\Serialize\Contracts\Attribute\AttributePropertyResolver;
use Attribute;
use UnitEnum;

#[Attribute(Attribute::TARGET_PROPERTY)]
class InputIgnore implements AttributePropertyResolver
{
    public array $groups;

    public function __construct(string|int|UnitEnum ...$groups)
    {
        $this->$groups = array_map(function ($name) {
            if ($name instanceof UnitEnum) {
                return $name->name;
            }
            return (string) $name;
        }, $groups);
    }
}
