<?php

use Astral\Serialize\Resolvers\InputValueCastResolver;
use Astral\Serialize\Resolvers\PropertyInputValueResolver;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Config\ConfigManager;

it('matches and removes the input name correctly when input names exist', function () {

    $collection = mock(DataCollection::class);

    $collection->shouldReceive('getInputNames')->andReturn(['input_name_1', 'input_name_2']);
    $collection->shouldReceive('getName')->andReturn('default_name');

    $payloadKeys = ['input_name_1' => 'value_1', 'extra_key' => 'value_2'];

    $resolver = new PropertyInputValueResolver(
        mock(ConfigManager::class),
        mock(InputValueCastResolver::class),
        []
    );

    $result = $resolver->matchInputName($collection, $payloadKeys);

    expect($result)->toBe('input_name_1');
});

it('matches and removes the default name when no input names exist', function () {
    // 创建一个 DataCollection Mock
    $collection = mock(DataCollection::class);

    $collection->shouldReceive('getInputNames')->andReturn([]);
    $collection->shouldReceive('getName')->andReturn('default_name');

    $payloadKeys = ['default_name' => 'value_1', 'extra_key' => 'value_2'];

    $resolver = new PropertyInputValueResolver(
        mock(ConfigManager::class),
        mock(InputValueCastResolver::class),
        []
    );

    $result = $resolver->matchInputName($collection, $payloadKeys);

    expect($result)->toBe('default_name');
});

it('returns false when no matching input name is found', function () {
    $collection = mock(DataCollection::class);

    $collection->shouldReceive('getInputNames')->andReturn(['input_name_1', 'input_name_2']);
    $collection->shouldReceive('getName')->andReturn('default_name');

    $payloadKeys = ['extra_key' => 'value_2'];

    $resolver = new PropertyInputValueResolver(
        mock(ConfigManager::class),
        mock(InputValueCastResolver::class),
        []
    );

    $result = $resolver->matchInputName($collection, $payloadKeys);

    expect($result)->toBeFalse();
});
