<?php

declare(strict_types=1);

namespace Astral\Serialize\Cast;

use UnitEnum;
use Astral\Serialize\Enums\TypeKindEnum;
use Astral\Serialize\Exceptions\ValueCastError;
use Astral\Serialize\Contracts\Attribute\InputValueCastInterface;
use Astral\Serialize\Support\Collections\DataCollection;
use BackedEnum;

class InputValueEnumCast implements InputValueCastInterface
{
    /**
     * @throws ValueCastError
     */
    public function resolve(DataCollection $dataCollection, mixed $value): string
    {
        if (is_string($value) && $dataCollection->getType()) {
            $enumClasses = [];
            $name = null;
            foreach ($dataCollection->getType() as $type) {
                if ($type->kind === TypeKindEnum::ENUM) {
                    $enumClasses[] = $type->className;
                    $enumInstance = $this->findEnumInstance($type->className, $value);
                    if ($enumInstance) {
                        return $enumInstance->name;
                    }
                }
            }

            if(count($dataCollection->getType()) === 1 && $enumClasses && $name === null) {
                throw new ValueCastError(
                    sprintf(
                        'Enum value "%s" not found in classes: %s',
                        $value,
                        implode('|', $enumClasses)
                    )
                );
            }

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
}
