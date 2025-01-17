<?php


use Astral\Serialize\Support\Mappers\CamelCaseMapper;
use Astral\Serialize\Support\Mappers\SnakeCaseMapper;
use Astral\Serialize\Annotations\DataCollection\InputIgnore;
use Astral\Serialize\Annotations\DataCollection\InputName;
use Astral\Serialize\Serialize;

beforeAll(function () {

    class InputNameObject extends Serialize
    {
        #[InputName('test_name')]
        public string $oneText;

        #[InputName(CamelCaseMapper::class)]
        public string $two_text;

        #[InputName(SnakeCaseMapper::class)]
        public string $threeText;
    }

    #[InputName(CamelCaseMapper::class)]
    class InputNameAllCamelMapper extends Serialize
    {
        public string $one_text;
        public string $two_text;
    }

    #[InputName(SnakeCaseMapper::class)]
    class InputNameAllSnakeMapper extends Serialize
    {
        public string $oneText;
        public string $twoText;

        public InputNameNestedSnakeMapper $nestedSnakeMapper;

    }

    #[InputName(SnakeCaseMapper::class)]
    class InputNameNestedSnakeMapper
    {
        public string $oneText;

        public string $twoText;
    }

    #[InputName(SnakeCaseMapper::class)]
    class InputNameAllSnakeAndDataInputNameMapper extends Serialize
    {
        #[InputName('test_name')]
        public string $oneText;

    }
});

it('test class CamelCaseMapper from serialize class', function () {

    $res = InputNameAllCamelMapper::from(
        oneText:'0',
        twoText:'123',
    );

    expect($res->getContext()->getChooseSerializeContext()->getProperty('one_text')->getInputName())->toBe('oneText')
        ->and($res->getContext()->getChooseSerializeContext()->getProperty('two_text')->getInputName())->toBe('twoText')
        ->and($res->one_text)->toBe('0')
        ->and($res->two_text)->toBe('123');

});

it('test class SnakeCaseMapper from serialize class', function () {

    $res = InputNameAllSnakeMapper::from(
        one_text:'0',
        two_text:'123',
        nested_snake_mapper:['one_text' => '456','two_text' => '789'],
    );

    expect($res->getContext()->getChooseSerializeContext()->getProperty('oneText')->getInputName())->toBe('one_text')
        ->and($res->getContext()->getChooseSerializeContext()->getProperty('twoText')->getInputName())->toBe('two_text')
        ->and($res->getContext()->getChooseSerializeContext()->getProperty('nestedSnakeMapper')->getInputName())->toBe('nested_snake_mapper')
        ->and($res->oneText)->toBe('0')
        ->and($res->twoText)->toBe('123')
        ->and($res->nestedSnakeMapper->oneText)->toBe('456')
        ->and($res->nestedSnakeMapper->twoText)->toBe('789');

});

it('test class InputNameAllSnakeAndDataInputNameMapper from serialize class', function () {

    $res = InputNameAllSnakeAndDataInputNameMapper::from(
        one_text:'0',
    );
    expect($res->getContext()->getChooseSerializeContext()->getProperty('oneText')->getInputName())->toBe('one_text')
        ->and($res->oneText)->toBe('0');

    $res = InputNameAllSnakeAndDataInputNameMapper::from(
        test_name:'1',
    );
    expect($res->getContext()->getChooseSerializeContext()->getProperty('oneText')->getInputName())->toBe('test_name')
        ->and($res->oneText)->toBe('1');

    $res = InputNameAllSnakeAndDataInputNameMapper::from(
        one_text:'2',
        test_name:'1',
    );
    expect($res->getContext()->getGroupCollection()->getProperties()['oneText']->getInputNames()['default'])->toHaveCount(3)
        ->and(current($res->getContext()->getGroupCollection()->getProperties()['oneText']->getInputNames()['default']))->toBe('oneText')
        ->and(next($res->getContext()->getGroupCollection()->getProperties()['oneText']->getInputNames()['default']))->toBe('test_name')
        ->and(end($res->getContext()->getGroupCollection()->getProperties()['oneText']->getInputNames()['default']))->toBe('one_text')
        ->and($res->getContext()->getChooseSerializeContext()->getProperty('oneText')->getInputName())->toBe('test_name')
        ->and($res->oneText)->toBe('1');

    $res = InputNameAllSnakeAndDataInputNameMapper::from(
        test_name:'1',
        one_text:'2',
    );

    expect($res->getContext()->getGroupCollection()->getProperties()['oneText']->getInputNames()['default'])->toHaveCount(3)
        ->and(current($res->getContext()->getGroupCollection()->getProperties()['oneText']->getInputNames()['default']))->toBe('oneText')
        ->and(next($res->getContext()->getGroupCollection()->getProperties()['oneText']->getInputNames()['default']))->toBe('test_name')
        ->and(end($res->getContext()->getGroupCollection()->getProperties()['oneText']->getInputNames()['default']))->toBe('one_text')
        ->and($res->getContext()->getChooseSerializeContext()->getProperty('oneText')->getInputName())->toBe('test_name')
        ->and($res->oneText)->toBe('1');

});


it('test InputName from serialize class', function () {

    $res = InputNameObject::from(
        test_name:'0',
        twoText:'123',
        three_text:'456',
    );

    expect($res->getContext()->getChooseSerializeContext()->getProperty('two_text')->getInputName())->toBe('twoText')
        ->and($res->getContext()->getChooseSerializeContext()->getProperty('threeText')->getInputName())->toBe('three_text')
        ->and($res->oneText)->toBe('0')
        ->and($res->two_text)->toBe('123')
        ->and($res->threeText)->toBe('456');

});
