<?php

declare(strict_types=1);

namespace Astral\Serialize\Cast\InputValue;

use Astral\Serialize\Enums\TypeKindEnum;
use Astral\Serialize\Exceptions\ValueCastError;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Contracts\Attribute\InputValueCastInterface;

class InputValueSetTypeCast implements InputValueCastInterface
{
    public function match(mixed $value, DataCollection $collection): bool
    {
        return $collection->getChooseType() === null;
    }

    /**
     * @throws ValueCastError
     */
    public function resolve(mixed $value, DataCollection $collection): mixed
    {

        $matchedType = $this->findMatchingType($value, $collection);

        if ($matchedType === null) {
            throw new ValueCastError(sprintf(
                'No matching type found for property [%s]  with value type [%s]. Supported types are: [%s].',
                $collection->getName(),
                gettype($value),
                implode('|', array_map(fn ($type) => $type->kind->name, $collection->getType()))
            ));
        }

        $collection->setChooseType($matchedType);
        return $value;
    }


    private function findMatchingType(mixed $value, DataCollection $collection): ?object
    {
        $type = gettype($value);
        foreach ($collection->getType() as $typeItem) {
            if ($typeItem->kind === TypeKindEnum::getNameTo($type)) {
                return $typeItem;
            }
        }

        return null;
    }


}
