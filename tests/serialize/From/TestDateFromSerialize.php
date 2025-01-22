<?php

use Astral\Serialize\Annotations\Input\InputDateFormat;
use Astral\Serialize\Serialize;
use Carbon\Carbon;

beforeAll(function () {
    class TestDateFromSerialize extends Serialize
    {
        #[InputDateFormat('Y-m-d H:i:s', 'd/m/Y')]
        public string $date_1;

        #[InputDateFormat('d/m/Y H:i:s', 'Y-m-d H:i:s')]
        public string $date_2;

        #[InputDateFormat('d/m/Y H:i:s')]
        public DateTime $date_3;

        #[InputDateFormat('d/m/Y')]
        public Carbon $date_4;

        public DateTime $date_5;

        public Carbon $date_6;

    }
});

it('test enum serialize class', function () {

    var_dump(class_exists(\Astral\Benchmarks\Fake\SerializeBenchFake::class));

    $object = TestDateFromSerialize::from([
        'date_1' => '2024-01-01 01:01:01',
        'date_2' => '2024-01-01 01:01:01',
    ]);

    $object2 = TestDateFromSerialize::from([
        'date_1' => '01/01/2024',
        'date_2' => '01/01/2024 00:00:01',
    ]);

    $object3 = TestDateFromSerialize::from([
        'date_3' => '01/01/2024 00:00:01',
        'date_4' => '01/02/2023',
        'date_5' => new DateTime(),
        'date_6' => new Carbon(),
    ]);

    expect($object->date_1)->toBe('01/01/2024')
        ->and($object->date_2)->toBe('2024-01-01 01:01:01')
        ->and($object2->date_1)->toBe('01/01/2024')
        ->and($object2->date_2)->toBe('2024-01-01 00:00:01')
        ->and($object3->date_3)->toBeInstanceOf(DateTime::class)
        ->and($object3->date_4)->toBeInstanceOf(Carbon::class)
        ->and($object3->date_5)->toBeInstanceOf(DateTime::class)
        ->and($object3->date_6)->toBeInstanceOf(Carbon::class)
        ->and($object3->date_3->format('Y-m-d H:i:s'))->toBe('2024-01-01 00:00:01')
        ->and($object3->date_4->format('Y-m-d'))->toBe('2023-02-01');
});
