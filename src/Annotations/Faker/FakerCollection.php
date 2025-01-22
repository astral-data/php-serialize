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

    /**
     * Resolves the faker collection data.
     *
     * @param DataCollection $collection
     * @return array
     */
    public function resolve(DataCollection $collection): array
    {
        return match (true) {
            is_array($this->fields)                                 => $this->generateArrayCollection(),
            is_string($this->fields) && class_exists($this->fields) => $this->generateClassCollection(),
            default                                                 => throw new InvalidArgumentException('Invalid fields type. Expected string or array.'),
        };
    }

    /**
     * Generate a collection of objects from a class.
     *
     * @return array
     */
    private function generateClassCollection(): array
    {
        $context = ContextFactory::build($this->fields);
        return array_map(fn () => $context->faker(), range(1, $this->num));
    }

    /**
     * Generate a nested array collection based on the fields.
     *
     * @return array
     */
    private function generateArrayCollection(): array
    {
        $faker             = $this->getFaker();
        $fakerDefaultRules = $this->getFakerDefaultRules();
        $typeCollection    = new TypeCollection(TypeKindEnum::STRING);

        return $this->generateNestedArray($faker, $fakerDefaultRules, $typeCollection, $this->fields);
    }

    /**
     * Recursively generate a nested array based on the given fields.
     *
     * @param Generator $faker
     * @param FakerDefaultRules $fakerDefaultRules
     * @param TypeCollection $typeCollection
     * @param array $fields
     * @return array
     */
    private function generateNestedArray(
        Generator $faker,
        FakerDefaultRules $fakerDefaultRules,
        TypeCollection $typeCollection,
        array $fields
    ): array {
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

    /**
     * Get the Faker generator instance.
     *
     * @return Generator
     */
    private function getFaker(): Generator
    {
        return SerializeContainer::get()->faker();
    }

    /**
     * Get the Faker default rules instance.
     *
     * @return FakerDefaultRules
     */
    private function getFakerDefaultRules(): FakerDefaultRules
    {
        return SerializeContainer::get()->fakerDefaultRules();
    }
}
