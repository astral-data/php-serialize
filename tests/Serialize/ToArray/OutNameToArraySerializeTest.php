<?php

use Astral\Serialize\Annotations\DataCollection\OutputName;
use Astral\Serialize\Serialize;
use Astral\Serialize\Support\Mappers\CamelCaseMapper;
use Astral\Serialize\Support\Mappers\SnakeCaseMapper;

beforeAll(function () {

    class OutNameObject extends Serialize
    {
        #[OutputName('test_name')]
        public ?string $oneText;

        #[OutputName(CamelCaseMapper::class)]
        public ?string $two_text;

        #[OutputName(SnakeCaseMapper::class)]
        public ?string $threeText;
    }

    #[OutputName(CamelCaseMapper::class)]
    class OutNameAllCamelMapper extends Serialize
    {
        public string $one_text;
        public string $two_text;
    }

    #[OutputName(SnakeCaseMapper::class)]
    class OutNameAllSnakeMapper extends Serialize
    {
        public string $oneText;
        public string $twoText;

    }

    #[OutputName(SnakeCaseMapper::class)]
    class OutNameAllSnakeAndDataOutNameMapper extends Serialize
    {
        #[OutputName('test_name')]
        public string $oneText;

    }
});

it('test class CamelCaseMapper from Serialize class', function () {

    $object     = new OutNameAllSnakeMapper();
    $res        =  $object->toArray();
    $properties = $object->getContext()->getGroupCollection()->getProperties();

    expect($properties['oneText']->getOutNames()['default'])->toHaveCount(1)
        ->and(current($properties['oneText']->getOutNames()['default']))->toBe('one_text')
        ->and($properties['twoText']->getOutNames()['default'])->toHaveCount(1)
        ->and(current($properties['twoText']->getOutNames()['default']))->toBe('two_text')
        ->and($res)->toHaveCount(2)
        ->and(array_key_exists('one_text', $res))->toBeTrue()
        ->and(array_key_exists('two_text', $res))->toBeTrue();

});

it('test class SnakeCaseMapper from Serialize class', function () {

    $object     = new OutNameAllCamelMapper();
    $res        =  $object->toArray();
    $properties = $object->getContext()->getGroupCollection()->getProperties();

    expect($properties['one_text']->getOutNames()['default'])->toHaveCount(1)
        ->and(current($properties['one_text']->getOutNames()['default']))->toBe('oneText')
        ->and($properties['two_text']->getOutNames()['default'])->toHaveCount(1)
        ->and(current($properties['two_text']->getOutNames()['default']))->toBe('twoText')
        ->and($res)->toHaveCount(2)
        ->and(array_key_exists('oneText', $res))->toBeTrue()
        ->and(array_key_exists('twoText', $res))->toBeTrue();

});

it('test class OutNameAllSnakeAndDataOutNameMapper from Serialize class', function () {

    $object     = new OutNameAllSnakeAndDataOutNameMapper();
    $res        =  $object->toArray();
    $properties = $object->getContext()->getGroupCollection()->getProperties();

    expect($properties['oneText']->getOutNames()['default'])->toHaveCount(2)
        ->and(current($properties['oneText']->getOutNames()['default']))->toBe('test_name')
        ->and(end($properties['oneText']->getOutNames()['default']))->toBe('one_text')
        ->and($res)->toHaveCount(2)
        ->and(array_key_exists('test_name', $res))->toBeTrue()
        ->and(array_key_exists('one_text', $res))->toBeTrue();

});

it('test OutputName from Serialize class', function () {

    $object     = new OutNameObject();
    $res        =  $object->toArray();
    $properties = $object->getContext()->getGroupCollection()->getProperties();

    expect($properties['oneText']->getOutNames()['default'])->toHaveCount(1)
        ->and(current($properties['oneText']->getOutNames()['default']))->toBe('test_name')
        ->and($properties['two_text']->getOutNames()['default'])->toHaveCount(1)
        ->and(current($properties['two_text']->getOutNames()['default']))->toBe('twoText')
        ->and($properties['threeText']->getOutNames()['default'])->toHaveCount(1)
        ->and(current($properties['threeText']->getOutNames()['default']))->toBe('three_text')
        ->and($res)->toHaveCount(3)
        ->and(array_key_exists('test_name', $res))->toBeTrue()
        ->and(array_key_exists('twoText', $res))->toBeTrue()
        ->and(array_key_exists('three_text', $res))->toBeTrue();

});
