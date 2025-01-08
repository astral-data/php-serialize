<?php

use Astral\Serialize\Annotations\Groups;
use Astral\Serialize\Serialize;
use Astral\Serialize\Support\Context\SerializeContext;
use Astral\Serialize\Annotations\DataCollection\InputName;
use Astral\Serialize\Annotations\DataCollection\InputIgnore;

beforeAll(function () {

    class OtherArrayNestedOne
    {
        public string $name_one;
        public int $id_one;

        /** @var array<int,OtherArrayNestedTwo> $otherNestedTwo */
        public array $otherNestedTwo;
    }

    class OtherArrayNestedTwo
    {
        public string $name_two;
        public int $id_two;

        /** @var array<OtherArrayNestedThree> $otherNestedThree */
        public array $otherNestedThree;
    }

    class OtherArrayNestedThree
    {
        #[InputName('name_three_other')]
        public string $name_three;
        #[InputIgnore(TestNestedSerialize::class, TestArrayNestedSerialize::class)]
        public int $id_three;
    }

    class TestArrayNestedSerialize extends Serialize
    {
        public string $name;

        public int $id;

        /** @var OtherArrayNestedOne[] $otherNestedOne  */
        public array $otherNestedOne;
    }

});

it('test parse array nested serialize class', function () {

    $startMemory = memory_get_usage();

    $instance = TestArrayNestedSerialize::from(
        [
            'name' => 'TestArrayNestedSerialize-name',
            'id' => 001,
            'otherNestedOne' => [
                [
                    'name_one' => 'OtherNestedOne-name-one-01',
                    'id_one' => 002,
                    'otherNestedTwo' => [
                        [
                            'name_two' => 'OtherNestedTwo-name-two-01',
                            'id_two' => 004,
                            'otherNestedThree' => [
                                [
                                    'name_three' => 'OtherNestedThree-name-three-01',
                                    'id_three' => 005
                                ],
                                [
                                    'name_three' => 'OtherNestedThree-name-three-02',
                                    'id_three' => 005
                                ]
                            ]
                        ],
                        [
                            'name_two' => 'OtherNestedTwo-name-two-02',
                            'id_two' => 004,
                            'otherNestedThree' => [
                                [
                                    'name_three_other' => 'OtherNestedThree-name-three-01',
                                    'id_three' => 005
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'name_one' => 'OtherNestedOne-name-one-02',
                    'id_one' => 003,
                    'otherNestedTwo' => [
                        [
                            'name_two' => 'OtherNestedTwo-name-two',
                            'id_two' => 006,
                            'otherNestedThree' => [
                                [
                                    'name_three_other' => 'OtherNestedThree-name-three',
                                    'id_three' => 007
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    );

    expect($instance->name)->toBe('TestArrayNestedSerialize-name')
        ->and($instance->id)->toBe(1);

    $firstNestedOne = $instance->otherNestedOne[0];
    expect($firstNestedOne->name_one)->toBe('OtherNestedOne-name-one-01')
        ->and($firstNestedOne->id_one)->toBe(2)
        ->and(count($firstNestedOne->otherNestedTwo))->toBe(2);

    $firstNestedTwo = $firstNestedOne->otherNestedTwo[0];
    expect($firstNestedTwo->name_two)->toBe('OtherNestedTwo-name-two-01')
        ->and($firstNestedTwo->id_two)->toBe(4)
        ->and(count($firstNestedTwo->otherNestedThree))->toBe(2);

    $firstNestedThree = $firstNestedTwo->otherNestedThree[0];
    expect($firstNestedThree->name_three)->toBe('OtherNestedThree-name-three-01');

    $secondNestedThree = $firstNestedTwo->otherNestedThree[1];
    expect($secondNestedThree->name_three)->toBe('OtherNestedThree-name-three-02');

    $secondNestedOne = $instance->otherNestedOne[1];
    expect($secondNestedOne->name_one)->toBe('OtherNestedOne-name-one-02')
        ->and($secondNestedOne->id_one)->toBe(3)
        ->and(count($secondNestedOne->otherNestedTwo))->toBe(1);

    $secondNestedTwo = $secondNestedOne->otherNestedTwo[0];
    expect($secondNestedTwo->name_two)->toBe('OtherNestedTwo-name-two')
        ->and($secondNestedTwo->id_two)->toBe(6)
        ->and(count($secondNestedTwo->otherNestedThree))->toBe(1);

    $thirdNestedThree = $secondNestedTwo->otherNestedThree[0];
    expect($thirdNestedThree->name_three)->toBe('OtherNestedThree-name-three');

//    $endMemory = memory_get_usage();
//
//    $peakMemory = memory_get_peak_usage();
//
//    $memoryUsed = $endMemory - $startMemory;
//
//    echo sprintf(
//        "Start Memory: %.2f MB\nEnd Memory: %.2f MB\nMemory Used: %.2f MB\nPeak Memory: %.2f MB\n",
//        $startMemory / 1024 / 1024,
//        $endMemory   / 1024 / 1024,
//        $memoryUsed  / 1024 / 1024,
//        $peakMemory  / 1024 / 1024
//    );
});
