<?php

declare(strict_types=1);

namespace Astral\Serialize\Cast\InputValue;

use Astral\Serialize\Contracts\Attribute\InputValueCastInterface;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Context\InputValueContext;
use Illuminate\Support\Str;

class InputValueSetActionCast implements InputValueCastInterface
{
    private ?string $setAction = null;

    public function match(mixed $value, DataCollection $collection, InputValueContext $context): bool
    {
        $this->setAction = null;

        if (!$collection->getChooseInputName()) {
            return false;
        }

        $this->setAction = 'set' . Str::studly($collection->getChooseInputName());
        if (!method_exists($context->currentObject, $this->setAction)) {
            return false;
        }

        return true;
    }

    public function resolve(mixed $value, DataCollection $collection, InputValueContext $context): mixed
    {
        return $context->currentObject->{$this->setAction}($value);
    }
}
