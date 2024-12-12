<?php

namespace Astral\Serialize\Support\TransFrom;

use Astral\Serialize\Annotations\InputName;
use Astral\Serialize\Contracts\Attribute\DataCollectionCastInterface;
use Astral\Serialize\Support\Collections\DataCollection;

class OutNameResolver implements DataCollectionCastInterface
{
    /**
     * @param InputName $cast
     */
    public function resolve(mixed $cast, DataCollection $dataCollection): void
    {
        $dataCollection->addOutName($cast->name);
    }
}
