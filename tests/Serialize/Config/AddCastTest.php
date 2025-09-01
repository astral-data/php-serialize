<?php


use Astral\Serialize\Annotations\DataCollection\InputName;
use Astral\Serialize\Casts\Normalizer\JsonNormalizerCast;
use Astral\Serialize\Enums\ConfigCastEnum;
use Astral\Serialize\Serialize;
use Astral\Serialize\Support\Config\ConfigManager;

beforeAll(function () {


    class AddCastTestClass extends Serialize
    {
        #[InputName('name_three_other')]
        public string $name_three;

        public int $id_three;
    }

});

it('test add cast to config', function () {

    $res = AddCastTestClass::from('{"name_three_other":"1223","id_three":3}');
    expect($res->toArray())->toHaveCount(2)
        ->and($res->toArray()['name_three'])->toBeNull()
    ->and($res->toArray()['id_three'])->toBeNull();

    ConfigManager::getInstance()->addCast(JsonNormalizerCast::class, ConfigCastEnum::INPUT_NORMALIZER);
    $res = AddCastTestClass::from('{"name_three_other":"1223","id_three":3}');
    expect($res->toArray())->toHaveCount(2)
        ->and($res->toArray()['name_three'])->toBe('1223')
        ->and($res->toArray()['id_three'])->toBe(3);

});
