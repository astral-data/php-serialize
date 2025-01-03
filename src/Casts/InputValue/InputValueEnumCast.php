<?php

declare(strict_types=1);

namespace Astral\Serialize\Casts\InputValue;

use Astral\Serialize\Contracts\Attribute\InputValueCastInterface;
use Astral\Serialize\Enums\TypeKindEnum;
use Astral\Serialize\Exceptions\ValueCastError;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Context\InputValueContext;
use BackedEnum;
use UnitEnum;

class InputValueEnumCast implements InputValueCastInterface
{
    public function match(mixed $value, DataCollection $collection, InputValueContext $context): bool
    {
        return $value && is_string($value) && $context->chooseSerializeContext->getProperty($collection->getName())->getType()?->kind == TypeKindEnum::ENUM;
    }

    /**
     * @throws ValueCastError
     */
    public function resolve(mixed $value, DataCollection $collection, InputValueContext $context): UnitEnum
    {

        $type          = $context->chooseSerializeContext->getProperty($collection->getName())->getType();
        $enumInstance  = $this->findEnumInstance($type->className, $value);
        if ($enumInstance) {
            return $enumInstance;
        }

        throw new ValueCastError(
            sprintf(
                'Enum value "%s" not found in classes: %s',
                $value,
                $type->className
            )
        );
    }

    /**
     * 查找枚举实例
     */
    private function findEnumInstance(string $className, string $value): ?UnitEnum
    {
        if (is_a($className, BackedEnum::class, true)
            || method_exists($className, 'tryFrom')) {
            return $className::tryFrom($value);
        }

        if (method_exists($className, 'cases')) {
            foreach ($className::cases() as $case) {
                if ($case->name === $value) {
                    return $case;
                }
            }
        }

        return null;
    }
}
