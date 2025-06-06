<?php

use Astral\Serialize\Serialize;

beforeAll(function () {

    class OtherOpenApiArrayNestedOne
    {
        public string $name_one;
        public int $id_one;

        public OtherOpenApiArrayNestedTwo $otherNestedTwo;
    }

    class OtherOpenApiArrayNestedTwo
    {
        public string $name_two;
        public int $id_two;

    }

    class TestOpenApiRequest extends Serialize
    {
        public string $name;
        public int $id;

        /** @var OtherOpenApiArrayNestedOne[] $otherNestedOne  */
        public array $otherNestedOne;
    }

    class TestOpenApiResponse extends Serialize
    {
        public string $name;
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


//        #[\Astral\Serialize\OpenApi\Annotations\Summary('测试方法二')]
//        #[\Astral\Serialize\OpenApi\Annotations\Route('test/two-action')]
//        #[\Astral\Serialize\OpenApi\Annotations\Response(TestOpenApiResponse::class)]
//        public function one2(TestOpenApiRequest $request): void
//        {
//        }
    }

});

//
it('test openapi build by class', function () {

       $api =  new  \Astral\Serialize\OpenApi\OpenApi();
       $api->buildByClass(TestOpenApiController::class);
       $res = $api->toString();
       print_r($res);

});
