<?php


use Astral\Serialize\Annotations\DataCollection\InputIgnore;
use Astral\Serialize\Annotations\DataCollection\InputName;
use Astral\Serialize\Serialize;

beforeAll(function () {
    class ArrayBestMatchOne
    {
        public string $name_one;
        public int $id_one;
    }

    class ArrayBestMatchTwo
    {
        public string $name_two;
        public int $id_two;
    }

    class ArrayBestMatchThree
    {
        #[InputName('name_three_other')]
        public string $name_three;
        #[InputIgnore(ArrayBestMatchSerialize::class)]
        public int $id_three;
    }

    class ArrayBestMatchSerialize extends Serialize
    {
        /** @var array<string,ArrayBestMatchOne|ArrayBestMatchTwo|ArrayBestMatchThree> */
        public array $vols_1;

        /** @var ArrayBestMatchOne[]|ArrayBestMatchTwo[]|ArrayBestMatchThree[] */
        public array $vols_3;

        /** @var (ArrayBestMatchOne|ArrayBestMatchTwo)[] */
        public array $vols_2;

        /** @var ArrayBestMatchOne[]|ArrayBestMatchTwo|ArrayBestMatchThree */
        public array|object $vols_4;
    }

});

it('test  array only one serialize nested serialize class', function () {

    $res = ArrayBestMatchSerialize::from(vols_1:[
        [
            'name_two' => 'ArrayBestMatchTwo-1',
            'id_two'   => 1,
        ],
        [
            'name_two' => 'ArrayBestMatchTwo-2',
            'id_two'   => 2,
        ],
        [
            'name_two' => 'ArrayBestMatchTwo-3',
            'id_two'   => 3,
        ],

    ]);

    expect($res)->toBeInstanceOf(ArrayBestMatchSerialize::class)
        ->and($res->vols_1)->toBeArray();


    foreach ($res->vols_1 as $date) {
        expect($date)->toBeInstanceOf(ArrayBestMatchTwo::class);
    }

    expect($res->vols_1)->toHaveCount(3)
        ->and($res->vols_1[0]->name_two)->toBe('ArrayBestMatchTwo-1')
        ->and($res->vols_1[0]->id_two)->toBe(1)
        ->and($res->vols_1[1]->name_two)->toBe('ArrayBestMatchTwo-2')
        ->and($res->vols_1[1]->id_two)->toBe(2)
        ->and($res->vols_1[2]->name_two)->toBe('ArrayBestMatchTwo-3')
        ->and($res->vols_1[2]->id_two)->toBe(3);
});

it('test array only one  serialize nested serialize class', function () {

    $res = ArrayBestMatchSerialize::from(vols_3:[
        [
            'name_two' => 'ArrayBestMatchTwo-1',
            'id_two'   => 1,
        ],
        [
            'name_two' => 'ArrayBestMatchTwo-2',
            'id_two'   => 2,
        ],
        [
            'name_two' => 'ArrayBestMatchTwo-3',
            'id_two'   => 3,
        ],

    ]);

    expect($res)->toBeInstanceOf(ArrayBestMatchSerialize::class)
        ->and($res->vols_3)->toBeArray();


    foreach ($res->vols_3 as $date) {
        expect($date)->toBeInstanceOf(ArrayBestMatchTwo::class);
    }

    expect($res->vols_3)->toHaveCount(3)
        ->and($res->vols_3[0]->name_two)->toBe('ArrayBestMatchTwo-1')
        ->and($res->vols_3[0]->id_two)->toBe(1)
        ->and($res->vols_3[1]->name_two)->toBe('ArrayBestMatchTwo-2')
        ->and($res->vols_3[1]->id_two)->toBe(2)
        ->and($res->vols_3[2]->name_two)->toBe('ArrayBestMatchTwo-3')
        ->and($res->vols_3[2]->id_two)->toBe(3);
});

it('test array analyzer serialize nested serialize class', function () {

    $res = ArrayBestMatchSerialize::from(vols_2:[
        'ArrayBestMatchOne' => [
            'name_one' => 'ArrayBestMatchOne',
            'id_one'   => 1,
        ],
        'ArrayBestMatchTwo' => [
            'name_two' => 'ArrayBestMatchTwo',
            'id_two'   => 2,
        ],
        'ArrayBestMatchThree' => [
            'name_three_other' => 'ArrayBestMatchThree',
            'id_three'         => 3,
        ]
    ]);

    expect($res)->toBeInstanceOf(ArrayBestMatchSerialize::class)
        ->and($res->vols_2)->toBeArray()
        ->and($res->vols_2)->toHaveKeys([
            'ArrayBestMatchOne',
            'ArrayBestMatchTwo',
            'ArrayBestMatchThree',
        ])
        ->and($res->vols_2['ArrayBestMatchOne'])->toBeInstanceOf(ArrayBestMatchOne::class)
        ->and($res->vols_2['ArrayBestMatchOne']->name_one)->toBe('ArrayBestMatchOne')
        ->and($res->vols_2['ArrayBestMatchOne']->id_one)->toBe(1)
        ->and($res->vols_2['ArrayBestMatchTwo'])->toBeInstanceOf(ArrayBestMatchTwo::class)
        ->and($res->vols_2['ArrayBestMatchTwo']->name_two)->toBe('ArrayBestMatchTwo')
        ->and($res->vols_2['ArrayBestMatchTwo']->id_two)->toBe(2)
        ->and($res->vols_2['ArrayBestMatchThree'])->toBeArray()
        ->and($res->vols_2['ArrayBestMatchThree']['name_three_other'])->toBe('ArrayBestMatchThree')
        ->and($res->vols_2['ArrayBestMatchThree']['id_three'])->toBe(3);
});


it('test array object serialize nested serialize class', function () {

    $res1 = ArrayBestMatchSerialize::from(vols_4:[
        'name_two' => 'ArrayBestMatchTwo',
        'id_two'   => 2,
    ]);

    $res2 = ArrayBestMatchSerialize::from(vols_4:[
        'name_three_other' => 'ArrayBestMatchTwo',
        'id_three'         => 3,
    ]);

    $res3 = ArrayBestMatchSerialize::from(vols_4:[
        [
            'name_one' => 'ArrayBestMatchTwo-1',
            'id_one'   => 1,
        ],
        [
            'name_one' => 'ArrayBestMatchTwo-2',
            'id_one'   => 2,
        ],
        [
            'name_one' => 'ArrayBestMatchTwo-3',
            'id_one'   => 3,
        ],
    ]);

    expect($res1)->toBeInstanceOf(ArrayBestMatchSerialize::class)
        ->and($res1->vols_4)->toBeInstanceOf(ArrayBestMatchTwo::class)
        ->and($res1->vols_4->name_two)->toBe('ArrayBestMatchTwo')
        ->and($res1->vols_4->id_two)->toBe(2)
        ->and($res2)->toBeInstanceOf(ArrayBestMatchSerialize::class)
        ->and($res2->vols_4)->toBeInstanceOf(ArrayBestMatchThree::class)
        ->and($res2->vols_4->name_three)->toBe('ArrayBestMatchTwo')
        ->and(isset($res2->vols_4->id_three))->toBeFalse()
        ->and($res3)->toBeInstanceOf(ArrayBestMatchSerialize::class)
        ->and($res3->vols_4)->toBeArray()
        ->and($res3->vols_4)->toHaveCount(3)
        ->and($res3->vols_4[0])->toBeInstanceOf(ArrayBestMatchOne::class)
        ->and($res3->vols_4[0]->name_one)->toBe('ArrayBestMatchTwo-1')
        ->and($res3->vols_4[0]->id_one)->toBe(1)
        ->and($res3->vols_4[1])->toBeInstanceOf(ArrayBestMatchOne::class)
        ->and($res3->vols_4[1]->name_one)->toBe('ArrayBestMatchTwo-2')
        ->and($res3->vols_4[1]->id_one)->toBe(2)
        ->and($res3->vols_4[2])->toBeInstanceOf(ArrayBestMatchOne::class)
        ->and($res3->vols_4[2]->name_one)->toBe('ArrayBestMatchTwo-3')
        ->and($res3->vols_4[2]->id_one)->toBe(3);

});
