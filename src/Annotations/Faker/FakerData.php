<?php

namespace Astral\Serialize\Annotations\Faker;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class FakerData
{
    public function __construct(string $method, mixed ... $params)
    {

    }
}
