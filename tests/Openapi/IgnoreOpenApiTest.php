<?php

use Astral\Serialize\OpenApi;
use Astral\Serialize\Serialize;

beforeAll(static function () {


    class TestIgnoreRequest extends Serialize
    {
        public string $name;

        public int $id;

        #[\Astral\Serialize\Annotations\DataCollection\InputIgnore]
        public int $input_ignore;
    }

    class TestIgnoreResponse extends Serialize
    {
        public string $name;

        public int $id;

        #[\Astral\Serialize\Annotations\DataCollection\OutputIgnore]
        public int $out_ignore;
    }


    #[\Astral\Serialize\OpenApi\Annotations\Tag('接口测试')]
    class TestIgnoreController
    {
        #[\Astral\Serialize\OpenApi\Annotations\Summary('测试方法一')]
        #[OpenApi\Annotations\Route('/test/ignore-route')]
        #[OpenApi\Annotations\Response(TestIgnoreResponse::class)]
        public function one(TestIgnoreRequest $request): void
        {
        }
    }

});

test('OpenAPI customer route', function () {

    $api =  new OpenApi();
    $api->buildByClass(TestIgnoreController::class);

    $openApi = $api::$openAPI;

    $paths       = $openApi->paths;
    $post        = $paths['/test/ignore-route']['post'];
    $requestBody = $post->requestBody;
    $schema      = $requestBody['content']['application/json']['schema'];

    expect($schema['properties']['input_ignore'] ?? null)->toBeNull();

    $schema = $post->responses[200]['content']['application/json']['schema'];
    expect($schema['properties']['out_ignore'] ?? null)->toBeNull();

});
