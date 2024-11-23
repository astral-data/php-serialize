<?php

namespace Astral\Serialize\Support\Collections;

use Illuminate\Support\Collection;

class DataGroupCollection
{

    public string $groupName;

    /**
     * @var class-string
     */
    public string $className;

    /** @var DataCollection[] */
    public array $properties;

    public function __construct(string $groupName, string $className)
    {
        $this->groupName = $groupName;
        $this->className = $className;
    }

    public function put(DataCollection $collection): void
    {
        $this->properties[] = $collection;
    }
}
