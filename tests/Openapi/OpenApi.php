<?php

use Astral\Serialize\OpenApi;
use Astral\Serialize\Serialize;

beforeAll(static function () {

    class OtherOpenApiArrayNestedOne
    {
        public string $name_one;
        public int $id_one;

        public OtherOpenApiArrayNestedTwo $object_test;

    }

    class OtherOpenApiArrayNestedTwo
    {
        public string $name_two;
        public int $id_two;

    }

    class TestOpenApiRequest extends Serialize
    {
        public string $name;
        public int|float|string $id;

        /** @var OtherOpenApiArrayNestedOne[]|OtherOpenApiArrayNestedTwo[]|string[] $any_array  */
        public array $any_array;
    }

    class TestOpenApiResponse extends Serialize
    {
        public ?string $name;
        public int $id;
    }

    #[\Astral\Serialize\OpenApi\Annotations\Tag('接口测试')]
    class TestOpenApiController{

        #[\Astral\Serialize\OpenApi\Annotations\Summary('测试方法一')]
        #[\Astral\Serialize\OpenApi\Annotations\Route('/test/one-action')]
        public function one(TestOpenApiRequest $request): TestOpenApiResponse
        {
           return new TestOpenApiResponse();
        }

    }

});

test('OpenAPI structure is correct', function () {

    $api =  new OpenApi();
    $api->buildByClass(TestOpenApiController::class);

    $openApi = $api::$OpenAPI;

    // 顶层结构断言
    expect($openApi->openapi)->toBe('3.1.1')
        ->and($openApi->info->version)->toBe('1.0.0')
        ->and($openApi->tags[0]->name)->toBe('接口测试');

    // 路径是否存在
    $paths = $openApi->paths;
    expect($paths)->toHaveKey('/test/one-action');

    // 方法与标签断言
    $post = $paths['/test/one-action']['post'];
    expect($post->summary)->toBe('测试方法一')
        ->and($post->tags)->toContain('接口测试');

    // 请求体断言
    $requestBody = $post->requestBody;
    expect($requestBody['required'])->toBeTrue();
    $schema = $requestBody['content']['application/json']['schema'];

    // 请求字段存在
    expect($schema['properties'])->toHaveKeys(['name', 'id', 'any_array']);

    // id 字段是 oneOf 并包含 string, integer, number
    $idOneOf = $schema['properties']['id']['oneOf'];
    $types = array_map(static fn($item) => $item['type']->value, $idOneOf);
    expect($types)->toMatchArray(['string', 'integer', 'number']);

    // any_array 是 oneOf 并包含至少一个 array 类型
    $anyArray = $schema['properties']['any_array'];
    expect($anyArray['type'])->toBe('oneOf')
        ->and($anyArray['oneOf'])->toBeArray()
        ->and($anyArray['oneOf'][0]['type'])->toBe('array');

    // 响应体 200 是否定义成功
    $response200 = $post->responses[200];
    expect($response200['description'])->toBe('成功');

    $schema = $response200['content']['application/json']['schema'];
    expect($schema['properties'])->toHaveKeys(['name', 'id'])
        ->and($schema['required'])->toHaveCount(1)
        ->and($schema['required'][0])->toBeString('id');

});