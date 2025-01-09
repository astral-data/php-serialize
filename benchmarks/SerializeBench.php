<?php

namespace Astral\Benchmarks;

use DateTime;
use Carbon\CarbonImmutable;
use PhpBench\Attributes\Revs;
use Astral\Serialize\Serialize;
use PhpBench\Attributes\Assert;
use Spatie\LaravelData\Optional;
use PhpBench\Attributes\Iterations;
use PhpBench\Attributes\BeforeMethods;
use Spatie\LaravelData\DataCollection;
use Astral\Benchmarks\Fake\NestedDataFake;
use Astral\Benchmarks\Fake\SerializeBenchFake;
use Astral\Benchmarks\Fake\NestedCollectionFake;
use Astral\Serialize\Support\Factories\CacheFactory;

require_once __DIR__ . '/../vendor/autoload.php';


class SerializeBench
{
    protected Serialize $object;

    protected array $objectPayload;

    public function setupObjectCreation(): void
    {
        $this->objectPayload = [
            'withoutType' => 'withoutType',
            'int' => 99,
            'bool' => true,
            'float' => 3.14,
            'string' => 'Hello World',
            'array' => [1, 1, 2, 3, 5, 8],
            'nullable' => null,
            'mixed' => 'test',
            'defaultDateTime' => new DateTime(),
            'stringDateTime' => '1994-05-16T12:00:00+01:00',
            'nestedCollection' => [
                [
                    'int' => 1,
                    'string' => 'apple',
                    'nestedData' => [
                        ['string' => 'banana'],
                        ['string' => 'cherry'],
                        ['string' => 'chert']
                    ],
                ],
                [
                    'int' => 2,
                    'string' => 'apple-2',
                    'nestedData' => [
                        ['string' => 'banana'],
                        ['string' => 'cherry'],
                        ['string' => 'chert']
                    ],
                ]
            ]
        ];
    }


    public function setupObject()
    {
        $this->object = new SerializeBenchFake();

        $this->object->withoutType = 'withoutType';
        $this->object->int = 99;
        $this->object->bool = true;
        $this->object->float = 3.14;
        $this->object->string = 'Hello World';
        $this->object->array = [1, 1, 2, 3, 5, 8];
        $this->object->nullable = null;
        $this->object->mixed = 'test';
        $this->object->defaultDateTime = new DateTime();
        $this->object->stringDateTime = '1994-05-16T12:00:00+01:00';
        $this->object->nestedCollection = [
            new NestedCollectionFake('998', 'string', [new NestedDataFake('I'),new NestedDataFake('Love'),new NestedDataFake('Your')]),
            new NestedCollectionFake('998', 'string', [new NestedDataFake('I'),new NestedDataFake('Love'),new NestedDataFake('Your')]),
            new NestedCollectionFake('998', 'string', [new NestedDataFake('I'),new NestedDataFake('Love'),new NestedDataFake('Your')]),
        ];
    }

    #[
        Revs(5000),
        Iterations(5),
        BeforeMethods([ 'setupObjectCreation']),
        Assert('mode(variant.time.avg) < 90 microseconds +/- 5%')
    ]
    public function benchObjectCreation(): void
    {
        SerializeBenchFake::from($this->objectPayload);
    }


    #[
        Revs(5000),
        Iterations(5),
        BeforeMethods([ 'setupObjectCreation']),
        Assert('mode(variant.time.avg) < 347 microseconds +/- 10%')
    ]
    public function benchObjectCreationWithoutCache(): void
    {
        CacheFactory::build()->clear();
        SerializeBenchFake::from($this->objectPayload);
    }

    #[
        Revs(500),
        Iterations(5),
        BeforeMethods(['setupObject']),
        Assert('mode(variant.time.avg) < 39 microseconds +/- 5%')
    ]
    public function benchObjectToArray(): void
    {
        $this->object->toArray();
    }

    #[
        Revs(5000),
        Iterations(5),
        BeforeMethods(['setupObject']),
        Assert('mode(variant.time.avg) < 226 microseconds +/- 10%')
    ]
    public function benchObjectToArrayWithoutCache(): void
    {
        CacheFactory::build()->clear();
        $this->object->toArray();
    }
}
