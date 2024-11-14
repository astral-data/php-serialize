<?php

declare(strict_types=1);

namespace Asrtal\Serialize\Annotations;

use Attribute;

/**
 * 映射前端的属性名称到后端的属性名称
 */
#[Attribute(Attribute::TARGET_PROPERTY|Attribute::TARGET_CLASS)]
class InputName
{
    /** @var string 转换的名称 */
    public string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
