<?php

namespace Astral\Serialize\Support\Collections;

use Illuminate\Support\Collection;

class DataGroupCollection  {

    /** @var DataCollection[] */
    public array $fields;


    public function put(DataCollection $collection):void
    {
        $this->fields[] = $collection;
    }
}