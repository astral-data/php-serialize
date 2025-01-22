<?php


use Astral\Serialize\Support\Instance\SerializeInstanceManager;

beforeAll(function () {
    class TestSerializeInstanceManagerTestClass
    {
    }

    class TestSerializeInstanceManagerTest1Class
    {
    }

    class TestSerializeInstanceManagerTest2Class
    {
    }
});

beforeEach(function () {
    $this->manager = new SerializeInstanceManager();
});

it('creates and retrieves an instance of a class', function () {

    $instance = $this->manager->get(TestSerializeInstanceManagerTestClass::class);

    expect($instance)->toBeInstanceOf(TestSerializeInstanceManagerTestClass::class);
});

it('returns the same instance on multiple get calls', function () {

    $instance1 = $this->manager->get(TestSerializeInstanceManagerTestClass::class);
    $instance2 = $this->manager->get(TestSerializeInstanceManagerTestClass::class);

    expect($instance1)->toBe($instance2);
});

it('throws an exception if the class does not exist', function () {
    $this->manager->get('NonExistentClass');
})->throws(InvalidArgumentException::class, 'Class NonExistentClass does not exist.');

it('clears a specific instance', function () {

    $instance1 = $this->manager->get(TestSerializeInstanceManagerTestClass::class);
    $this->manager->clear(TestSerializeInstanceManagerTestClass::class);
    $instance2 = $this->manager->get(TestSerializeInstanceManagerTestClass::class);

    expect($instance1)->not->toBe($instance2);
});

it('clears all instances', function () {

    $instance1 = $this->manager->get(TestSerializeInstanceManagerTest1Class::class);
    $instance2 = $this->manager->get(TestSerializeInstanceManagerTest2Class::class);

    $this->manager->clear();

    $newInstance1 = $this->manager->get(TestSerializeInstanceManagerTest1Class::class);
    $newInstance2 = $this->manager->get(TestSerializeInstanceManagerTest2Class::class);

    expect($instance1)->not->toBe($newInstance1)
        ->and($instance2)->not->toBe($newInstance2);
});
