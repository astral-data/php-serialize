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
        return $value && is_string($value) && $this->hasEnumType($collection);
    }

    /**
     * @throws ValueCastError
     */
    public function resolve(mixed $value, DataCollection $collection, InputValueContext $context): UnitEnum|string
    {

        $types = $collection->getTypes();
        foreach ($types as $type) {
            if ($type->kind != TypeKindEnum::ENUM) {
                continue;
            }
            $enumInstance  = $this->findEnumInstance($type->className, $value);
            if ($enumInstance) {
                return $enumInstance;
            }
        }

        if (count($types) == 1) {
            throw new ValueCastError(
                sprintf(
                    'Enum value "%s" not found in EnumClass: %s',
                    $value,
                    current($types)?->className
                )
            );
        }

        return $value;
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

    private function hasEnumType(DataCollection $collection): bool
    {
        foreach ($collection->getTypes() as $type) {
            if ($type->kind == TypeKindEnum::ENUM) {
                return true;
            }
        }
        return false;
    }
}
