<?php

declare(strict_types=1);

namespace Astral\Serialize\OpenApi\Collections;

use Astral\Serialize\Enums\TypeKindEnum;
use Astral\Serialize\OpenApi\Enum\ParameterTypeEnum;
use Astral\Serialize\Support\Collections\TypeCollection;
use Attribute;

class ParameterCollection
{
    public function __construct(
        public string $className,
        /** @var string 元素变量名 */
        public string       $name,
        /** @var string descriptions  */
        public string       $descriptions = '',
        /** @var TypeCollection[] $types */
        public array $types,
        public ParameterTypeEnum $type,
        /** @var mixed 示例值 */
        public mixed        $example = '',
        /** @var bool 是否必填 */
        public bool         $required = false,
        /** @var bool 是否忽略显示 */
        public bool         $ignore = false,
        /** @var array<ParameterCollection[]> $children */
        public array        $children  = [],
    ){
    }

    public function addChildren(array $collections,ParameterTypeEnum $type = ParameterTypeEnum::STRING): void
    {
        $children = new ParameterChildrenCollection();
        $children->type = $type;
        $children->children = $collections;
        $this->children[] = $children;
    }
}

