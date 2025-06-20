<?php

namespace Astral\Serialize\OpenApi\Storage;

/**
 * 创建结构
 */
class TreeNode
{
    /**
     * @var mixed
     */
    private mixed $value;

    /**
     * @var TreeNode[]
     */
    private array $children = [];

    public function __construct($value = null)
    {
        $this->setValue($value);
    }

    public function addChildren(TreeNode $child): void
    {
        $this->children[] = $child;
    }

    public function setValue($value): void
    {
        $this->value = $value;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * @return TreeNode[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }
}
