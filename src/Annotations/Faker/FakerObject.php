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

        if (is_string($this->fields) && class_exists($this->fields)) {
            return ContextFactory::build($this->fields)->faker();
        }

        $faker             = SerializeContainer::get()->faker();
        $fakerDefaultRules = SerializeContainer::get()->fakerDefaultRules();
        $typeCollection    =  new TypeCollection(TypeKindEnum::STRING);
        return $this->generateNestedArray($faker, $fakerDefaultRules, $typeCollection, $this->fields);
    }

    private function generateNestedArray(Generator $faker, FakerDefaultRules $fakerDefaultRules, TypeCollection $typeCollection, array $fields): array
    {
        $nested = [];

        foreach ($fields as $field => $value) {
            if (is_array($value)) {
                $nested[$field] =   $this->generateNestedArray($faker, $fakerDefaultRules, $typeCollection, $value);
            } else {
                $nested[$value] = $fakerDefaultRules->resolve([$typeCollection], $field);
            }
        }

        return $nested;
    }
}
