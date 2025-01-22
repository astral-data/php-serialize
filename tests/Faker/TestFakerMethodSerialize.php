<?php

use Astral\Serialize\Annotations\Faker\FakerMethod;
use Astral\Serialize\Serialize;
use Astral\Serialize\Annotations\Faker\FakerObject;

beforeAll(function () {

    class TestFakerMethod
    {
        public function __construct(
            public readonly string $string,
            #[FakerObject(['name','id','children' => ['child-id','child-name']])]
            public readonly array $array,
        ) {
        }
    }

    class TestFakerService
    {
        public function testMethod(TestFakerMethod $request): string
        {
            return $request->string . '-abc';
        }

        public function testMethodObject(TestFakerMethod $request): TestFakerMethod
        {
            return $request;
        }

        public function testMethodObjectAndParams(TestFakerMethod $request, array $vols): array
        {
            return $vols;
        }
    }



    class TestFakerWithConstructor
    {
        public function __construct(
            public readonly TestFakerService $testFakerService,
        ) {
        }

        public function testMethod(TestFakerMethod $request): string
        {
            return $this->testFakerService->testMethod($request) . '-def';
        }
    }

    class TestFakerA
    {
        public function __construct(public readonly TestFakerDependencyChainRepeat $b)
        {

        }
    }

    class TestFakerDependencyChainRepeat
    {
        public function __construct(public readonly TestFakerA $a)
        {
        }

        public function testMethod(TestFakerA $a): string
        {
            return 'abc';
        }
    }


    class TestFakerMethodSerialize extends Serialize
    {
        #[FakerMethod(TestFakerService::class, 'testMethod')]
        public string $testMethod;

        #[FakerMethod(TestFakerService::class, 'testMethodObject', returnType: 'array')]
        public array $testMethodObject;

        #[FakerMethod(TestFakerService::class, 'testMethodObject', returnType: 'array.children.child-id')]
        public string $testMethodObject_2;

        #[FakerMethod(
            TestFakerService::class,
            'testMethodObjectAndParams',
            params: [
                'vols' => ['id' => 1,'name' => 'vols','children' => [
                    'child-id' => 2,
                    'child-name' => 'child-vols'
                ]]
            ],
            returnType: 'children.child-name'
        )]
        public string $testMethodObjectAndParams;

        #[FakerMethod(TestFakerWithConstructor::class, 'testMethod')]
        public string $testConstructorMethod;

    }

    class TestFakerDependencyChainRepeatMethodSerialize extends Serialize
    {
        #[FakerMethod(TestFakerDependencyChainRepeat::class, 'testMethod')]
        public string $testMethod;

    }

});
it('test faker method serialize class', function () {
    $res = TestFakerMethodSerialize::faker();

    // Assert that the resolved data matches the expected structure and values
    expect($res)->toBeInstanceOf(TestFakerMethodSerialize::class)
        ->and($res->testMethod)->toBeString()->toContain('-abc')
        ->and($res->testMethodObject)->toBeArray()
            ->toHaveKey('name')
            ->toHaveKey('id')
            ->toHaveKey('children')
        ->and($res->testMethodObject_2)->toBeString()
        ->and($res->testMethodObjectAndParams)->toBe('child-vols')
        ->and($res->testConstructorMethod)->toBeString()->toContain('-abc-def');
});

it('test faker method dependency chain class', function () {
    // This test should throw an exception due to circular dependency
    TestFakerDependencyChainRepeatMethodSerialize::faker();
})->throws(Exception::class, 'Circular dependency detected: TestFakerDependencyChainRepeat -> TestFakerA -> TestFakerDependencyChainRepeat');
