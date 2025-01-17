<?php

declare(strict_types=1);

namespace Astral\Serialize\Casts\OutValue;

use Astral\Serialize\Contracts\Attribute\OutValueCastInterface;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Context\OutContext;
use Illuminate\Support\Str;

class OutValueGetterCast implements OutValueCastInterface
{
    public function match($value, DataCollection $collection, OutContext $context): bool
    {
        $actionName = Str::camel($collection->getName() . 'Getter');
        return method_exists($context->className, $actionName);
    }

    public function resolve(mixed $value, DataCollection $collection, OutContext $context): string
    {
        $actionName = Str::camel($collection->getName() . 'Getter');
        return $context->classInstance->{$actionName};
    }
}
