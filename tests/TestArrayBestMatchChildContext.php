<?php


use Astral\Serialize\Annotations\Groups;
use Astral\Serialize\Serialize;
use Astral\Serialize\Support\Context\SerializeContext;
use Astral\Serialize\Annotations\DataCollection\InputName;
use Astral\Serialize\Annotations\DataCollection\InputIgnore;

beforeEach(function () {

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
        public array $vols;

//        /** @var ArrayBestMatchOne[]|ArrayBestMatchTwo[]|ArrayBestMatchThree[] */
        /** @var (ArrayBestMatchOne|ArrayBestMatchTwo)[] */
        public array $dates;
    }

});

it('test parse array nested serialize class', function () {

    $startMemory = memory_get_usage();



    $endMemory = memory_get_usage();

    $peakMemory = memory_get_peak_usage();


    $memoryUsed = $endMemory - $startMemory;

    $res = ArrayBestMatchSerialize::from(dates:[
        [
            'name_two' => 'ArrayBestMatchTwo-1',
            'id_two' => 1,
        ],
        [
            'name_two' => 'ArrayBestMatchTwo-2',
            'id_two' => 2,
        ],
        [
            'name_two' => 'ArrayBestMatchTwo-3',
            'id_two' => 3,
        ],

    ]);

    
    $res = ArrayBestMatchSerialize::from(vols:[
        'ArrayBestMatchOne' => [
            'name_one' => 'ArrayBestMatchOne',
            'id_one' => 1,
        ],
        'ArrayBestMatchTwo' => [
            'name_two' => 'ArrayBestMatchTwo',
            'id_two' => 1,
        ],
        'ArrayBestMatchThree' => [
            'name_three_other' => 'ArrayBestMatchThree',
            'id_three' => 1,
        ]
    ]);

//    var_dump($res);

    echo sprintf(
        "Start Memory: %.2f MB\nEnd Memory: %.2f MB\nMemory Used: %.2f MB\nPeak Memory: %.2f MB\n",
        $startMemory / 1024 / 1024,
        $endMemory / 1024 / 1024,
        $memoryUsed / 1024 / 1024,
        $peakMemory / 1024 / 1024
    );
});
