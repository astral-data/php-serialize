<?php

declare(strict_types=1);

namespace Astral\Serialize\Annotations;

use Attribute;
use UnitEnum;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_CLASS)]
class Groups
{
    public array $names;
    public function __construct(array|string $names)
    {
        $names = is_string($names) ? [$names] : $names;

        $this->names = array_map(function ($name) {
            if ($name instanceof UnitEnum) {
                return $name->name;
            }
            return (string) $name;
        }, $names);
    }
}
