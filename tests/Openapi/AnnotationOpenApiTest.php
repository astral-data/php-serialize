<?php

use Astral\Serialize\OpenApi;
use Astral\Serialize\Serialize;

beforeAll(static function () {

    enum AnnotationOpenApiEnum
    {
        case ENUM_1;
        case ENUM_2;
    }


    class AnnotationOpenApiRequest extends Serialize
    {
        #[OpenApi\Annotations\OpenApi('description test enum')]
        public AnnotationOpenApiEnum $test_enum;

        #[OpenApi\Annotations\OpenApi(description:'this is object description', example:'1')]
        public object $test_object;

        #[OpenApi\Annotations\OpenApi(example:'abc')]
        public string $test_example;

    }

    #[\Astral\Serialize\OpenApi\Annotations\Tag('接口测试')]
    class AnnotationOpenApiController
    {
        #[\Astral\Serialize\OpenApi\Annotations\Summary('测试方法一')]
        #[\Astral\Serialize\OpenApi\Annotations\Route('/test/description-action')]
        public function one(AnnotationOpenApiRequest $request): void
        {
        }
    }

});

test('OpenAPI build description', function () {
    $api = new OpenApi();
    $api->buildByClass(AnnotationOpenApiController::class);

    $openApi     = $api::$openAPI;
    $paths       = $openApi->paths;
    $post        = $paths['/test/description-action']['post'];
    $requestBody = $post->requestBody;
    $schema      = $requestBody['content']['application/json']['schema'];

    expect(array_keys($schema['properties']))->toMatchArray([
        'test_enum',
        'test_object',
        'test_example',
    ]);

    $enumProp = $schema['properties']['test_enum'];
    expect($enumProp['type'])->toBe('string')
        ->and($enumProp['description'])->toBe('description test enum optional values：ENUM_1、ENUM_2')
        ->and($enumProp['example'])->toBe('');

    $objProp = $schema['properties']['test_object'];
    expect($objProp['type'])->toBe('object')
        ->and($objProp['description'])->toBe('this is object description')
        ->and($objProp['example'])->toBe('1');

    $exProp = $schema['properties']['test_example'];
    expect($exProp['type'])->toBe('string')
        ->and($exProp['description'])->toBe('')
        ->and($exProp['example'])->toBe('abc')
        ->and($schema['required'])->toMatchArray([
            'test_enum',
            'test_object',
            'test_example',
        ]);
});
