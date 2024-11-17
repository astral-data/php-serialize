<?php

declare(strict_types=1);

namespace Astral\Serialize\Tests;

use Astral\Serialize\Exception\SerializeException;

use PHPUnit\Framework\TestCase;
use stdClass;

class SerializeTest extends TestCase
{
    public function testFromObject(): void
    {
        $classNotDoc = new class
        {
            /**
             * @var string prop
             */
            public $prop1;

            public $prop2;
        };

        $classObject = new class
        {
            /**
             * @var string prop
             */
            public $prop1;

            /**
             * @var string prop
             */
            public $prop2;

            /**
             * @var float prop
             */
            public $prop3;

            /**
             * @var bool prop
             */
            public $prop4;

            /**
             * @var string[] prop
             */
            public $prop5;

            /**
             * @var stdClass prop
             */
            public $prop6;
        };

        // Create a mock object with public properties
        $mockObject = (object) [
            'prop1' => null,
            'prop2' => '',
            'prop3' => 0,
            'prop4' => false,
            'prop5' => ['1', '2', '3'],
            'prop6' => new stdClass,
        ];

        $this->expectException(SerializeException::class);
        $this->expectExceptionMessage('docComment is null Property [ prop2 ] must add doc comment');
        $result = (new Serialize)->fromObject($classNotDoc, $mockObject);

        $result = (new Serialize)->fromObject($classObject, $mockObject);
        foreach ($result as $propertyName => $expectedValue) {
            $this->assertEquals($expectedValue, $result->{$propertyName});
        }
    }

    public function testGetPropertyAlisaGroup(): void
    {

        $serialize = new Serialize;
        $object = $serialize->fromJson(TestGroup::class, '{
            "pid":"test1",
            "names": "Example Test",
            "lists": [
                {
                "id": "1",
                "name": "Item 1",
                "groups": {
                        "groupsTwo": "Groups A"
                    }
                },
                {
                "id": "2",
                "name": "Item 2",
                "groups": {
                        "groupsTwo": "Groups B"
                    }
                }
            ]
        }');

        $this->assertEquals(['names', 'id', 'name', 'groupsTwo'], $serialize->getFlattenPropertiesByGroup('test', $object));
        $this->assertEquals(['test-alisa-2', 'id', 'name', 'groupsTwo'], $serialize->getFlattenPropertiesAlisaByGroup('test', $object));
        $this->assertEquals('test-alisa-2', $serialize->getPropertyAlisaByGroup('names', 'test', $object));
    }
}
