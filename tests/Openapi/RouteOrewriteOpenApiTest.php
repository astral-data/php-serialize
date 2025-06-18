<?php

use Astral\Serialize\OpenApi;
use Astral\Serialize\Serialize;

beforeAll(static function () {


    #[Attribute(Attribute::TARGET_METHOD)]
    class CustomerRoute extends OpenApi\Annotations\Route
    {

    }

    class TestCustomerRouteRequest extends Serialize
    {
        public string $name;

        public int $id;
    }

    #[\Astral\Serialize\OpenApi\Annotations\Tag('接口测试')]
    class TestCustomerRouteController{

        #[\Astral\Serialize\OpenApi\Annotations\Summary('测试方法一')]
        #[CustomerRoute('/test/customer-route')]
        public function one(TestCustomerRouteRequest $request): void
        {
        }

    }

});

test('OpenAPI customer route', function () {

    $api =  new OpenApi();
    $api->buildByClass(TestCustomerRouteController::class);

    $openApi = $api::$OpenAPI;

    // 路径是否存在
    $paths = $openApi->paths;
    expect($paths)->toHaveKey('/test/customer-route');

});