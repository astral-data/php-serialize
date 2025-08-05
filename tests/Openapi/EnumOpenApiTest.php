<?php

use Astral\Serialize\OpenApi;
use Astral\Serialize\Serialize;

beforeAll(static function () {

    enum OpenapiEnum
    {
        case ENUM_1;
        case ENUM_2;
    }

    enum OpenapiUnionEnum
    {
        case ENUM_2;
        case ENUM_3;

        case ENUM_4;

    }



    class OpenapiEnumRequest extends Serialize
    {
        public OpenapiEnum $test_enum;

        public OpenapiEnum|OpenapiUnionEnum $test_string_enum;

        public OpenapiEnum|OpenapiUnionEnum|string $test_string_2_enum;

        public OpenapiEnum|OpenapiUnionEnum|string|int $test_one_of_enum;

    }

    #[\Astral\Serialize\OpenApi\Annotations\Tag('接口测试')]
    class OpenapiEnumController
    {
        #[\Astral\Serialize\OpenApi\Annotations\Summary('测试方法一')]
        #[\Astral\Serialize\OpenApi\Annotations\Route('/test/enum-action')]
        public function one(OpenapiEnumRequest $request): void
        {
        }
    }

});

test('OpenAPI enums auto create description', function () {

    $api =  new OpenApi();
    $api->buildByClass(OpenapiEnumController::class);

    $openApi = $api::$openAPI;


    $paths       = $openApi->paths;
    $post        = $paths['/test/enum-action']['post'];
    $requestBody = $post->requestBody;
    $schema      = $requestBody['content']['application/json']['schema'];


    expect($schema)->toHaveKey('properties')
        ->and($schema['properties'])
        ->toHaveKeys([
            'test_enum',
            'test_string_enum',
            'test_string_2_enum',
            'test_one_of_enum',
        ])
        ->and($schema['properties']['test_enum'])->toMatchArray([
            'type' => 'string',
            'description' => 'optional values：ENUM_1、ENUM_2',
            'example' => '',
        ])
        ->and($schema['properties']['test_string_enum'])->toMatchArray([
            'type' => 'string',
            'description' => 'optional values：ENUM_1、ENUM_2、ENUM_3、ENUM_4',
            'example' => '',
        ])
        ->and($schema['properties']['test_string_2_enum'])->toMatchArray([
            'type' => 'string',
            'description' => 'optional values：ENUM_1、ENUM_2、ENUM_3、ENUM_4',
            'example' => '',
        ])
        ->and($schema['properties']['test_one_of_enum'])->toMatchArray([
            'type' => 'oneOf',
            'description' => 'optional values：ENUM_1、ENUM_2、ENUM_3、ENUM_4',
            'example' => '',
        ])
        ->and($schema['properties']['test_one_of_enum']['oneOf'])->toBeArray()->toHaveCount(2)
        ->and($schema['properties']['test_one_of_enum']['oneOf'][0])->toMatchArray(['type' => 'string'])
        ->and($schema['properties']['test_one_of_enum']['oneOf'][1])->toMatchArray(['type' => 'integer'])
        ->and($schema['required'])->toBeArray()->toEqualCanonicalizing([
            'test_enum',
            'test_string_enum',
            'test_string_2_enum',
            'test_one_of_enum',
        ]);

});
