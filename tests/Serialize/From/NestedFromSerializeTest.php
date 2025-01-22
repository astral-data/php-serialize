<?php

use Astral\Serialize\Annotations\DataCollection\InputIgnore;
use Astral\Serialize\Serialize;

beforeAll(function () {

    class OtherNestedOne
    {
        public string $name_one;
        public int $id_one;

        public OtherNestedTwo $otherNestedTwo;
    }

    class OtherNestedTwo
    {
        public string $name_two;
        public int $id_two;
        public OtherNestedThree $otherNestedThree;
    }

    class OtherNestedThree
    {
        public string $name_three;
        #[InputIgnore(TestNestedSerialize::class)]
        public int $id_three;
    }

    class TestNestedSerialize extends Serialize
    {
        public string $name;

        public int $id;

        public OtherNestedOne $otherNestedOne;

    }

});

it('test parse nested Serialize class', function () {
    $instance = TestNestedSerialize::from(
        [
            'name'           => 'TestNestedSerialize-name',
            'id'             => 001,
            'otherNestedOne' => [
                'name_one'       => 'OtherNestedOne-name_one',
                'id_one'         => 002,
                'otherNestedTwo' => [
                    'name_two'         => 'OtherNestedTwo-name_two',
                    'id_two'           => 003,
                    'otherNestedThree' => [
                        'name_three' => 'OtherNestedThree-name_three',
                        'id_three'   => 004
                    ]
                ]
            ]
        ]
    );

    // Add assertions to validate the parsed data
    expect($instance)->toBeInstanceOf(TestNestedSerialize::class)
        ->and($instance->name)->toBe('TestNestedSerialize-name')
        ->and($instance->id)->toBe(1)
        ->and($instance->otherNestedOne)->toBeInstanceOf(OtherNestedOne::class)
        ->and($instance->otherNestedOne->name_one)->toBe('OtherNestedOne-name_one')
        ->and($instance->otherNestedOne->id_one)->toBe(2)
        ->and($instance->otherNestedOne->otherNestedTwo)->toBeInstanceOf(OtherNestedTwo::class)
        ->and($instance->otherNestedOne->otherNestedTwo->name_two)->toBe('OtherNestedTwo-name_two')
        ->and($instance->otherNestedOne->otherNestedTwo->id_two)->toBe(3)
        ->and($instance->otherNestedOne->otherNestedTwo->otherNestedThree)->toBeInstanceOf(OtherNestedThree::class)
        ->and($instance->otherNestedOne->otherNestedTwo->otherNestedThree->name_three)->toBe('OtherNestedThree-name_three')
        ->and(isset($instance->otherNestedOne->otherNestedTwo->otherNestedThree->id_three))->toBeFalse(); // Ensure id_three is ignored

});
