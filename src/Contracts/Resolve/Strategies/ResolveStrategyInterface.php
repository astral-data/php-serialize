<?php

namespace Astral\Serialize\Contracts\Resolve\Strategies;

use Astral\Serialize\Support\Collections\DataCollection;

interface ResolveStrategyInterface
{
    public function supports(DataCollection $collection, $value): bool;
    public function resolve(DataCollection $collection, $value): mixed;
}
