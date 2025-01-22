<?php

namespace Astral\Serialize\Annotations\Faker;

use Astral\Serialize\Contracts\Attribute\FakerCastInterface;
use Astral\Serialize\Enums\TypeKindEnum;
use Astral\Serialize\Faker\Rule\FakerDefaultRules;
use Astral\Serialize\SerializeContainer;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Collections\TypeCollection;
use Astral\Serialize\Support\Factories\ContextFactory;
use Attribute;
use Faker\Generator;
use InvalidArgumentException;

#[Attribute(Attribute::TARGET_PROPERTY)]
class FakerObject implements FakerCastInterface
{
    /**
     * @param array|class-string $fields
     */
    public function __construct(
        public array|string $fields
    ) {
    }

    public function resolve(DataCollection $collection): array|object
    {
        return match (true) {
            is_string($this->fields) && class_exists($this->fields) => $this->buildFakerFromClass($this->fields),
            is_array($this->fields)                                 => $this->generateNestedArray(
                $this->getFaker(),
                $this->getFakerDefaultRules(),
                new TypeCollection(TypeKindEnum::STRING),
                $this->fields
            ),
            default => throw new InvalidArgumentException('Invalid fields type. Expected string or array.')
        };
    }

    private function buildFakerFromClass(string $className): object
    {
        return ContextFactory::build($className)->faker();
    }

    private function getFaker(): Generator
    {
        return SerializeContainer::get()->faker();
    }

    private function getFakerDefaultRules(): FakerDefaultRules
    {
        return SerializeContainer::get()->fakerDefaultRules();
    }

    private function generateNestedArray(
        Generator $faker,
        FakerDefaultRules $fakerDefaultRules,
        TypeCollection $typeCollection,
        array $fields
    ): array {
        $nested = [];

        foreach ($fields as $field => $value) {
            if (is_array($value)) {
                $nested[$field] = $this->generateNestedArray($faker, $fakerDefaultRules, $typeCollection, $value);
            } else {
                $nested[$value] = $fakerDefaultRules->resolve([$typeCollection], $field);
            }
        }

        return $nested;
    }
}
