<?php

namespace Astral\Serialize\Annotations\Faker;

use Astral\Serialize\Contracts\Attribute\FakerCastInterface;
use Astral\Serialize\SerializeContainer;
use Astral\Serialize\Support\Collections\DataCollection;
use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class FakerValue implements FakerCastInterface
{
    private array $params;

    public function __construct(public string $method, mixed ... $params)
    {
        $this->params = $params;
    }

    public function resolve(DataCollection $collection): mixed
    {
        return SerializeContainer::get()->faker()->{$this->method}(...$this->params);
    }
}
