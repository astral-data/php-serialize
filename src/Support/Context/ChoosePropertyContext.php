<?php

namespace Astral\Serialize\Support\Context;

use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Collections\TypeCollection;

class ChoosePropertyContext
{
    private ?string $inputName    = null;
    private ?array $outputName    = null;
    private ?TypeCollection $type = null;

    /** @var ChooseSerializeContext[] $children */
    private array $children = [];

    public function __construct(
        private readonly string $name,
        private readonly DataCollection $dataCollection,
        private readonly ?ChooseSerializeContext $parent = null,
    ) {

    }

    public static function build(DataCollection $collection, ChooseSerializeContext $context, DataCollection $dataCollection): ChoosePropertyContext
    {
        return new self($collection->getName(), $dataCollection, $context);
    }

    public function getDataCollection(): DataCollection
    {
        return $this->dataCollection;
    }

    public function getParent(): ?ChooseSerializeContext
    {
        return $this->parent;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getInputName(): ?string
    {
        return $this->inputName;
    }

    public function setInputName(?string $inputName): void
    {
        $this->inputName = $inputName;
    }

    public function getOutPutNames(): array
    {
        return $this->outputName;
    }

    public function setOutPutNames(array $outPutNames): void
    {
        $this->outputName = $outPutNames;
    }

    public function getType(): ?TypeCollection
    {
        return $this->type;
    }

    public function setType(?TypeCollection $type): void
    {
        $this->type = $type;
    }

    /**
     * @return array<string|int,ChooseSerializeContext>
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function addChildren(ChooseSerializeContext $context, string|int $key = 0): ChooseSerializeContext
    {
        $this->children[$key] = $context;

        return $this->children[$key];
    }
}
