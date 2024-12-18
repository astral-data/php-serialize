<?php

namespace Astral\Serialize\Support\Collections;

use Astral\Serialize\Enums\TypeKindEnum;

class TypeCollection
{
    public TypeKindEnum $kind;

    /** @var class-string $className */
    public ?string $className;

    public function __construct(TypeKindEnum $kind, ?string $className = null)
    {
        $this->kind      = $kind;
        $this->className = $className;
    }

    public function toArray(): array
    {
        return [
            'kind'      => $this->kind->name,
            'className' => $this->className,
        ];
    }
}
