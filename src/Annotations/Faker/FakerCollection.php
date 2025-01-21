<?php

namespace Astral\Serialize\Annotations\Faker;

use Astral\Serialize\Support\Factories\ContextFactory;
use Astral\Serialize\Support\Context\FakerValueContext;
use Astral\Serialize\Contracts\Attribute\FakerCastInterface;
use Astral\Serialize\Enums\TypeKindEnum;
use Astral\Serialize\Faker\Rule\FakerDefaultRules;
use Astral\Serialize\SerializeContainer;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Collections\TypeCollection;
use Attribute;
use Faker\Generator;

#[Attribute(Attribute::TARGET_PROPERTY)]
class FakerCollection implements FakerCastInterface
{
    /**
     * @param array|class-string $fields
     */
    public function __construct(
        public string|array $fields,
        public int $num = 1,
    ) {
    }

    public function resolve(DataCollection $collection): array
    {
        return match (true) {
            is_array($this->fields) => $this->resolveArrayFields(),
            is_string($this->fields) && class_exists($this->fields) => $this->resolveClass(),
            default => null,
        };
    }

    private function resolveClass(): array
    {

        $vols = [];
        $context = ContextFactory::build($this->fields);
        for ($i = 0 ; $i < $this->num ; $i++) {
            $vols[$i] = $context->faker();
        }

        return $vols;
    }

    private function resolveArrayFields(): array
    {
        $faker             = SerializeContainer::get()->faker();
        $fakerDefaultRules = SerializeContainer::get()->fakerDefaultRules();
        $typeCollection    =  new TypeCollection(TypeKindEnum::STRING);
        return $this->generateNestedArray($faker, $fakerDefaultRules, $typeCollection, $this->fields);
    }


    private function generateNestedArray(Generator $faker, FakerDefaultRules $fakerDefaultRules, TypeCollection $typeCollection, array $fields): array
    {
        $nested = [];

        for ($i = 0 ; $i < $this->num ; $i++) {
            $nested[$i] = [];
            foreach ($fields as $field => $value) {
                if (is_array($value)) {
                    $nested[$i][$field] =   $this->generateNestedArray($faker, $fakerDefaultRules, $typeCollection, $value);
                } else {
                    $nested[$i][$value] = $fakerDefaultRules->resolve([$typeCollection], $field);
                }
            }
        }

        return $nested;
    }
}
