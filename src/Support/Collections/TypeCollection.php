<?php

namespace Astral\Serialize\Support\Collections;

use Astral\Serialize\Enums\TypeKindEnum;

class TypeCollection
{
    public function __construct(
        public TypeKindEnum $kind,
        /** @var class-string $className */
        public ?string $className = null
    ) {

    }

    public function toArray(): array
    {
        return [
            'kind'      => $this->kind->name,
            'className' => $this->className,
        ];
    }
}
