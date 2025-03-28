<?php

declare(strict_types=1);

namespace Astral\Serialize\Casts\InputValue;

use Astral\Serialize\Contracts\Attribute\InputValueCastInterface;
use Astral\Serialize\Enums\TypeKindEnum;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Context\InputValueContext;
use stdClass;

class InputValueNullCast implements InputValueCastInterface
{
    public function match(mixed $value, DataCollection $collection, InputValueContext $context): bool
    {
        return  $value === null && $collection->isNullable() === false && count($collection->getTypes()) > 0 ;
    }

    public function resolve(mixed $value, DataCollection $collection, InputValueContext $context): mixed
    {
        return match ($collection->getTypes()[0]->kind) {
            TypeKindEnum::FLOAT , TypeKindEnum::INT => 0,
            TypeKindEnum::ARRAY ,  TypeKindEnum::COLLECT_SINGLE_OBJECT => [],
            TypeKindEnum::OBJECT , TypeKindEnum::CLASS_OBJECT => new stdClass(),
            TypeKindEnum::STRING => '',
            default              => null,
        };
    }
}
