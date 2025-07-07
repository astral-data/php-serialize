<?php

use Astral\Serialize\OpenApi;
use Astral\Serialize\Serialize;

beforeAll(static function () {


    class TestBaseGroupRequest extends Serialize
    {

        public int $input_group_0;

        #[\Astral\Serialize\Annotations\Groups('group_1')]
        public int $input_group_1_11;

        #[\Astral\Serialize\Annotations\Groups('group_1')]
        public string $input_group_1_22;

        #[\Astral\Serialize\Annotations\Groups('group_1','group_2')]
        #[\Astral\Serialize\Annotations\DataCollection\InputName('input_change_group_1_33_2_11',['group_2'])]
        public string $input_group_1_33_2_11;

        #[\Astral\Serialize\Annotations\Groups('group_2')]
        public string $input_group_2_22;

        #[\Astral\Serialize\Annotations\Groups('group_2')]
        public string $input_group_2_33;

    }

    class TestBaseGroupResponse extends Serialize
    {
        public int $out_group_0;

        #[\Astral\Serialize\Annotations\Groups('group_1')]
        public int $out_group_1_11;

        #[\Astral\Serialize\Annotations\Groups('group_1')]
        public string $out_group_1_22;

        #[\Astral\Serialize\Annotations\Groups('group_1','group_2')]
        public string $out_group_1_33_2_11;

        #[\Astral\Serialize\Annotations\Groups('group_2')]
        public string $out_group_2_22;

        #[\Astral\Serialize\Annotations\Groups('group_2')]
        public string $out_group_2_33;
    }


    #[\Astral\Serialize\OpenApi\Annotations\Tag('接口测试')]
    class TestBaseGroupController
    {
        #[\Astral\Serialize\OpenApi\Annotations\Summary('测试方法一')]
        #[OpenApi\Annotations\Route('/test/base-group')]
        #[OpenApi\Annotations\Response(TestBaseGroupResponse::class, groups:['group_1'])]
        #[OpenApi\Annotations\RequestBody(groups: ['group_2'])]
        public function one(TestBaseGroupRequest $request): void
        {
        }
    }

});

test('OpenAPI customer route', function () {

    $api =  new OpenApi();
    $api->buildByClass(TestBaseGroupController::class);

    $openApi = $api::$openAPI;

    $paths       = $openApi->paths;
    $post        = $paths['/test/base-group']['post'];
    $requestBody = $post->requestBody;
    $schema      = $requestBody['content']['application/json']['schema'];
    expect($schema['properties'])->toHaveCount(3)->toHaveKeys(['input_change_group_1_33_2_11', 'input_group_2_22', 'input_group_2_33']);


    $schema = $post->responses[200]['content']['application/json']['schema']['properties']['data'];
    expect($schema['properties'])->toHaveCount(3)->toHaveKeys(['out_group_1_11', 'out_group_1_22', 'out_group_1_33_2_11']);

});
