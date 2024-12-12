<?php

namespace Astral\Serialize\Support\TransFrom;

use Astral\Serialize\Annotations\InputIgnore;
use Astral\Serialize\Contracts\Attribute\DataCollectionCastInterface;
use Astral\Serialize\Support\Collections\DataCollection;

class OutIgnoreResolver implements DataCollectionCastInterface
{
    public function __construct(
    ) {

    }

    /**
     * @param InputIgnore $cast
     */
    public function resolve(mixed $cast, DataCollection $dataCollection): void
    {
        if(!$cast->groups || in_array($dataCollection->getParentGroupCollection()->getGroupName(), $cast->groups)) {
            $dataCollection->setOutIgnore(true);
        }
    }
}
