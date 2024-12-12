<?php

namespace Astral\Serialize\Support\TransFrom;

use Astral\Serialize\Annotations\InputName;
use Astral\Serialize\Contracts\Attribute\DataCollectionCastInterface;
use Astral\Serialize\Support\Collections\DataCollection;

class InputNameResolver implements DataCollectionCastInterface
{
    /**
     * @param InputName $cast
     */
    public function resolve(mixed $cast, DataCollection $dataCollection): void
    {
        if(!$cast->groups || in_array($dataCollection->getParentGroupCollection()->getGroupName(), $cast->groups)) {
            $dataCollection->addInputName($cast->name);
        }

    }
}
