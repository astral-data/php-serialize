<?php

declare(strict_types=1);

namespace Astral\Serialize\Annotations;

use Attribute;

/**
 * toArray输出的属性名称
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_CLASS)]
class OutName
{
    /** @var string 转换的名称 */
    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
