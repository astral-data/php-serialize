<?php

declare(strict_types=1);

namespace Astral\Serialize\OpenApi\Collections;

use Astral\Serialize\Enums\TypeKindEnum;
use Astral\Serialize\OpenApi\Enum\ParameterTypeEnum;
use Attribute;

class ParameterCollection
{
    public function __construct(
        /** @var string 元素变量名 */
        public string       $name,
        /** @var string descriptions  */
        public string       $descriptions = '',
        public ParameterTypeEnum $type = ParameterTypeEnum::STRING,
        /** @var mixed 示例值 */
        public mixed        $example = '',
        /** @var bool 是否必填 */
        public bool         $required = false,
        /** @var bool 是否忽略显示 */
        public bool         $ignore = false,
        /** @var array<string, ParameterCollection> $children */
        public array        $children  = [],
    ){
    }
}
