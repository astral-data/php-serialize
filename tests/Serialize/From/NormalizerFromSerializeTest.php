<?php

use Astral\Serialize\Serialize;

beforeAll(function () {
    class NormalizerOne extends Serialize
    {
        public string $name_one;
        public int $id_one;
    }

    class NormalizerTwo extends Serialize
    {
        public string $name_two;
        public int $id_two;
    }

    class NormalizerClass extends Serialize
    {
        public NormalizerOne $one;
        public NormalizerTwo $two;
        public mixed $three;
    }

});

it('test normalizer Serialize class', function () {

    $normalizerOne = new NormalizerOne();
    $normalizerOne->name_one = 'one name';
    $normalizerOne->id_one = 1;

    $normalizerTwo = new NormalizerTwo();
    $normalizerTwo->name_two = 'two name';
    $normalizerTwo->id_two = 2;

    $res = NormalizerClass::from(one: $normalizerOne, two: $normalizerTwo, three: $normalizerOne);

    expect($res->one)->toBeInstanceOf(NormalizerOne::class)
        ->and($res->one->name_one)->toBe('one name')
        ->and($res->one->id_one)->toBe(1)
        ->and($res->two)->toBeInstanceOf(NormalizerTwo::class)
        ->and($res->two->name_two)->toBe('two name')
        ->and($res->two->id_two)->toBe(2)
        ->and($res->three)->toBeArray()
        ->and($res->three)->toMatchArray([
            'name_one' => 'one name',
            'id_one' => 1
        ]);


});

it('test json_encode Serialize class', function () {

    $normalizerOne = new NormalizerOne();
    $normalizerOne->name_one = 'one name';
    $normalizerOne->id_one = 1;

    $normalizerTwo = new NormalizerTwo();
    $normalizerTwo->name_two = 'two name';
    $normalizerTwo->id_two = 2;

    $res = NormalizerClass::from(one: $normalizerOne, two: $normalizerTwo, three: $normalizerOne);
    $resJson = json_encode($res);
    expect($resJson)->toBe('{"code":200,"message":"\u64cd\u4f5c\u6210\u529f","data":{"one":{"name_one":"one name","id_one":1},"two":{"name_two":"two name","id_two":2},"three":{"name_one":"one name","id_one":1}}}');

    $res->setMessage('233');
    $resJson = json_encode($res);
    expect($resJson)->toBe('{"code":200,"message":"233","data":{"one":{"name_one":"one name","id_one":1},"two":{"name_two":"two name","id_two":2},"three":{"name_one":"one name","id_one":1}}}');

    $res->setCode(-1);
    $resJson = json_encode($res);
    expect($resJson)->toBe('{"code":-1,"message":"233","data":{"one":{"name_one":"one name","id_one":1},"two":{"name_two":"two name","id_two":2},"three":{"name_one":"one name","id_one":1}}}');

    $resJson = $res->withoutResponseToJsonString();
    expect($resJson)->toBe('{"one":{"name_one":"one name","id_one":1},"two":{"name_two":"two name","id_two":2},"three":{"name_one":"one name","id_one":1}}');

    $resJson = $res->toJsonString();
    expect($resJson)->toBe('{"code":-1,"message":"233","data":{"one":{"name_one":"one name","id_one":1},"two":{"name_two":"two name","id_two":2},"three":{"name_one":"one name","id_one":1}}}');

});

