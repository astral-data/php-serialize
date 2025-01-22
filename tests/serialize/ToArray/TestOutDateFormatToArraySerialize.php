<?php

use Astral\Serialize\Annotations\Out\OutDateFormat;
use Astral\Serialize\Serialize;
use Carbon\Carbon;

beforeAll(function () {

    class TestOutDateFormat extends Serialize
    {
        #[OutDateFormat('d/m/Y')]
        public string $date_1;

        #[OutDateFormat]
        public string $date_2;

        #[OutDateFormat('d/m/Y H:i:s')]
        public DateTime $date_3;

        #[OutDateFormat('d/m/Y')]
        public Carbon $date_4;

        public DateTime $date_5;

        public Carbon $date_6;
    }
});

it('test out data format to array serialize class', function () {
    // Create a new instance of the TestOutDateFormat class
    $object = new TestOutDateFormat();

    // Set up test data
    $object->date_1 = '2023-10-05'; // String date
    $object->date_2 = '2023-10-05'; // String date with default format
    $object->date_3 = new DateTime('2023-10-05 14:30:00'); // DateTime object
    $object->date_4 = Carbon::create(2023, 10, 5); // Carbon object
    $object->date_5 = new DateTime('2023-10-05'); // DateTime object without annotation
    $object->date_6 = Carbon::create(2023, 10, 5); // Carbon object without annotation

    // Serialize the object to an array
    $result = $object->toArray();

    // Assertions
    expect($result['date_1'])->toBe('05/10/2023') // Formatted with 'd/m/Y'
    ->and($result['date_2'])->toBe('2023-10-05 00:00:00') // Default format Y-m-d H:i:s
    ->and($result['date_3'])->toBe('05/10/2023 14:30:00') // Formatted with 'd/m/Y H:i:s'
    ->and($result['date_4'])->toBe('05/10/2023') // Formatted with 'd/m/Y'
    ->and($result['date_5'])->toBeInstanceOf(DateTime::class) // Default DateTime format
    ->and($result['date_6'])->toBeInstanceOf(Carbon::class); // Default Carbon format
});
