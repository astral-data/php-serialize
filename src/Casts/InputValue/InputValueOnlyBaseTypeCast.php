<?php

declare(strict_types=1);

namespace Astral\Serialize\Casts\InputValue;

use Astral\Serialize\Contracts\Attribute\InputValueCastInterface;
use Astral\Serialize\Enums\TypeKindEnum;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Context\InputValueContext;
use stdClass;

class InputValueOnlyBaseTypeCast implements InputValueCastInterface
{
    public function match(mixed $value, DataCollection $collection, InputValueContext $context): bool
    {
        return $value && $collection->isNullable() === false && count($collection->getTypes()) == 1 ;
    }

    public function resolve(mixed $value, DataCollection $collection, InputValueContext $context): mixed
    {

//        if($context->getTypes()[0]->kind == TypeKindEnum::FLOAT){
//            print_r($context);
//        }

        return match ($collection->getTypes()[0]->kind) {
            TypeKindEnum::INT  => (int)$value,
            TypeKindEnum::FLOAT => (float)$value,
            TypeKindEnum::STRING => (string)$value,
            TypeKindEnum::BOOLEAN => (bool)$value,
            default              => $value,
        };
    }
}
