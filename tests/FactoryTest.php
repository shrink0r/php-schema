<?php

namespace Shrink0r\Configr\Tests;

use PHPUnit_Framework_TestCase;
use Shrink0r\Configr\Exception;
use Shrink0r\Configr\Factory;
use Shrink0r\Configr\Property\BoolProperty;
use Shrink0r\Configr\Property\IntProperty;
use Shrink0r\Configr\Property\ScalarProperty;
use Shrink0r\Configr\Property\StringProperty;
use Shrink0r\Configr\SchemaInterface;

class FactoryTest extends PHPUnit_Framework_TestCase
{
    public function testCreateProperty()
    {
        $factory = new Factory();
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();
        $property = $factory->createProperty([ 'type' => 'scalar', 'name' => 'foo' ], $mockSchema);

        $this->assertInstanceOf(ScalarProperty::class, $property);
        $this->assertEquals('foo', $property->getName());
        $this->assertTrue($property->isRequired());
        $this->assertEquals($mockSchema, $property->getSchema());
    }

    public function testCreateProperies()
    {
        $factory = new Factory();
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();
        $properties = $factory->createProperties(
            [
                'username' => [ 'type' => 'string' ],
                'active' => [ 'type' => 'bool' ],
                'login_attempts' => [ 'type' => 'int' ]
            ],
            $mockSchema
        );

        $this->assertCount(3, $properties);
        $this->assertInstanceOf(StringProperty::class, $properties['username']);
        $this->assertInstanceOf(BoolProperty::class, $properties['active']);
        $this->assertInstanceOf(IntProperty::class, $properties['login_attempts']);
    }

    public function testCreateSchema()
    {
        $factory = new Factory();
        $schema = $factory->createSchema(
            'login_attempts',
            [
                'type' => 'assoc',
                'properties' => [
                    'username' => [ 'type' => 'string' ],
                    'active' => [ 'type' => 'bool' ],
                    'attempt_count' => [ 'type' => 'int' ]
                ]
            ]
        );

        $this->assertInstanceOf(SchemaInterface::class, $schema);
    }

    public function testCreateWithNonExistingClass()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(
            "Class 'Shrink0r\Configr\Tests\Void' that has been registered for type 'scalar' does not exist"
        );

        $factory = new Factory([ 'scalar' => Void::class ]);
    }

    public function testCreatePropertyWithMissingType()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Missing required key 'type' within property definition.");

        $factory = new Factory();
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();
        $factory->createProperty([ 'name' => 'foo' ], $mockSchema);
    }

    public function testCreatePropertyWithMissingName()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Missing required key 'name' within property definition.");

        $factory = new Factory();
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();
        $factory->createProperty([ 'type' => 'scalar' ], $mockSchema);
    }

    public function testCreatePropertyWithNonRegisteredType()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Given property type 'foo' has not been registered.");

        $factory = new Factory();
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();
        $factory->createProperty([ 'type' => 'foo', 'name' => 'bar' ], $mockSchema);
    }
}
