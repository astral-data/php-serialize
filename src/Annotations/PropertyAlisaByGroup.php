<?php

declare(strict_types=1);

namespace Asrtal\Serialize\Annotations;

use Attribute;

/**
 * 转换属性的名称
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class PropertyAlisaByGroup
{
    public string $name;

    public array $groups;

    public function __construct(string $name, array $groups = [])
    {
        $this->name = $name;

        $this->groups = $groups;
    }
}
