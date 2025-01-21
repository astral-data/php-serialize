<?php

namespace Astral\Serialize\Faker\Rule;

use Astral\Serialize\Enums\TypeKindEnum;
use Astral\Serialize\Support\Collections\TypeCollection;
use Astral\Serialize\Support\Factories\MapperFactory;
use Astral\Serialize\Support\Mappers\SnakeCaseMapper;
use Faker\Generator;
use UnitEnum;

class FakerDefaultRules
{
    public function __construct(
        public readonly Generator $faker
    ) {

    }

    private array $rules = [
        ['type' => [TypeKindEnum::STRING,TypeKindEnum::MIXED], 'method' => 'regex', 'pattern' => '/avatar|icon/i', 'faker' => 'imageUrl', 'params' => [100, 100]],
        ['type' => [TypeKindEnum::STRING,TypeKindEnum::MIXED], 'method' => 'regex', 'pattern' => '/image|img|photo|pic/i', 'faker' => 'imageUrl', 'params' => [400, 400]],
        ['type' => [TypeKindEnum::STRING,TypeKindEnum::MIXED], 'method' => 'wildcard', 'pattern' => '*url', 'faker' => 'url', 'params' => []],
        ['type' => [TypeKindEnum::STRING,TypeKindEnum::MIXED], 'method' => 'regex', 'pattern' => '/nick|user_?name/i', 'faker' => 'name', 'params' => []],
        ['type' => [TypeKindEnum::STRING,TypeKindEnum::MIXED], 'method' => 'regex', 'pattern' => '/title|name/i', 'faker' => 'sentence', 'params' => [3]],
        ['type' => [TypeKindEnum::STRING, TypeKindEnum::INT,TypeKindEnum::FLOAT,TypeKindEnum::MIXED], 'method' => 'regex', 'pattern' => '/id|num|code|amount|quantity|price|discount|balance|money/i', 'faker' => 'numberBetween', 'params' => [1, 100]],
        ['type' => [TypeKindEnum::STRING, TypeKindEnum::INT,TypeKindEnum::FLOAT,TypeKindEnum::MIXED], 'method' => 'regex', 'pattern' => '/phone|mobile|tel$/i', 'faker' => 'phoneNumber', 'params' => []],
        ['type' => [TypeKindEnum::STRING,TypeKindEnum::MIXED], 'method' => 'wildcard', 'pattern' => '*date', 'faker' => 'date', 'params' => ['Y-m-d']],
        ['type' => [TypeKindEnum::STRING, TypeKindEnum::INT,TypeKindEnum::FLOAT,TypeKindEnum::MIXED], 'method' => 'wildcard', 'pattern' => '*date', 'faker' => 'date', 'params' => ['Ymd']],
        ['type' => [TypeKindEnum::STRING, TypeKindEnum::INT,TypeKindEnum::FLOAT,TypeKindEnum::MIXED], 'method' => 'regex', 'pattern' => '/created?_?at|updated?_?at|deleted?_?at|.*time/i', 'faker' => 'unixTime', 'params' => []],
        ['type' => [TypeKindEnum::STRING,TypeKindEnum::MIXED,TypeKindEnum::CLASS_OBJECT], 'method' => 'regex', 'pattern' => '/created?_?at|updated?_?at|deleted?_?at|.*time/i', 'faker' => 'dateTime', 'params' => ['Y-m-d H:i:s']],
        ['type' => [TypeKindEnum::STRING,TypeKindEnum::MIXED], 'method' => 'regex', 'pattern' => '/e?mail*/i', 'faker' => 'email', 'params' => []],
        ['type' => [TypeKindEnum::STRING,TypeKindEnum::MIXED], 'method' => 'wildcard', 'pattern' => '*province*', 'faker' => 'state', 'params' => []],
        ['type' => [TypeKindEnum::STRING,TypeKindEnum::MIXED], 'method' => 'wildcard', 'pattern' => '*city*', 'faker' => 'city', 'params' => []],
        ['type' => [TypeKindEnum::STRING,TypeKindEnum::MIXED], 'method' => 'wildcard', 'pattern' => '*address', 'faker' => 'address', 'params' => []],
        ['type' => [TypeKindEnum::STRING,TypeKindEnum::MIXED], 'method' => 'wildcard', 'pattern' => '*district', 'faker' => 'citySuffix', 'params' => []],
        ['type' => [TypeKindEnum::STRING,TypeKindEnum::MIXED], 'method' => 'wildcard', 'pattern' => '*ip', 'faker' => 'ipv4', 'params' => []],
        ['type' => [TypeKindEnum::STRING,TypeKindEnum::MIXED], 'method' => 'wildcard', 'pattern' => 'birthday', 'faker' => 'date', 'params' => ['Y-m-d']],
        ['type' => [TypeKindEnum::STRING, TypeKindEnum::INT,TypeKindEnum::FLOAT,TypeKindEnum::MIXED], 'method' => 'wildcard', 'pattern' => 'birthday', 'faker' => 'date', 'params' => ['Ymd']],
        ['type' => [TypeKindEnum::STRING,TypeKindEnum::MIXED], 'method' => 'regex', 'pattern' => '/gender|sex/i', 'faker' => 'randomElement', 'params' => [['男', '女']]],
        ['type' => [TypeKindEnum::STRING,TypeKindEnum::MIXED], 'method' => 'wildcard', 'pattern' => 'description', 'faker' => 'paragraph', 'params' => []],
        ['type' => [TypeKindEnum::INT,TypeKindEnum::FLOAT], 'method' => 'wildcard', 'pattern' => '*', 'faker' => 'numberBetween', 'params' => [1, 100]],
        ['type' => [TypeKindEnum::STRING,TypeKindEnum::MIXED], 'method' => 'wildcard', 'pattern' => '*', 'faker' => 'sentence', 'params' => [4]],
        ['type' => [TypeKindEnum::ENUM], 'method' => 'wildcard', 'pattern' => '*', 'faker' => 'randomElement'],
        ['type' => [TypeKindEnum::ARRAY], 'method' => 'wildcard', 'pattern' => '*', 'faker' => 'randomElement'],
    ];

    /**
     * @param TypeCollection[] $types
     * @param string $name
     * @return mixed
     */
    public function resolve(array $types, string $name): mixed
    {

        $mapper = MapperFactory::build(SnakeCaseMapper::class);
        $name   =  $mapper->resolve($name);

        foreach ($types as $type) {

            foreach ($this->rules as $rule) {
                if ($this->matchRule($type->kind, $name, $rule)) {

                    if ($type->kind === TypeKindEnum::ENUM) {
                        return $this->generateEnumValue($type->className);
                    }

                    return $this->generateMockValue($rule['faker'], $rule['params']);
                }
            }
        }

        return null;
    }

    private function matchRule(TypeKindEnum $type, string $name, array $rule): bool
    {

        if (!in_array($type, $rule['type'])) {
            return false;
        }

        // Match using regex or wildcard
        if ($rule['method'] === 'regex') {
            return preg_match($rule['pattern'], $name) === 1;
        } elseif ($rule['method'] === 'wildcard') {
            return fnmatch($rule['pattern'], $name);
        }

        return false;
    }

    private function generateMockValue(string $method, array $params): mixed
    {
        return $this->faker->{$method}(...$params);
    }

    private function generateEnumValue(null|string|UnitEnum $enumClass): mixed
    {
        if (!is_string($enumClass) || !enum_exists($enumClass)) {
            throw new \InvalidArgumentException('Invalid enum class provided for ENUM type.');
        }

        $enumInstances = $enumClass::cases();
        return $this->faker->randomElement($enumInstances);
    }
}
