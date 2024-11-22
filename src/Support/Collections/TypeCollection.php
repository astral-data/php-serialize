<?php

namespace Astral\Serialize\Support\Collections;

use Astral\Serialize\Enums\TypeKindEnum;
use Illuminate\Support\Collection;
use ReflectionProperty;

class TypeCollection
{
    public TypeKindEnum $kind;
    public ?string $className;


    public function __construct(TypeKindEnum $kind, ?string $className = null)
    {
        $this->kind = $kind;
        $this->className = $className;
    }
}
