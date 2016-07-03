<?php

namespace Shrink0r\PhpSchema\Tests;

use PHPUnit_Framework_TestCase;
use Shrink0r\PhpSchema\Factory;
use Shrink0r\PhpSchema\Property\BoolProperty;
use Shrink0r\PhpSchema\Property\IntProperty;
use Shrink0r\PhpSchema\Property\ScalarProperty;
use Shrink0r\PhpSchema\Property\StringProperty;
use Shrink0r\PhpSchema\SchemaInterface;

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

    /**
     * @expectedException \Shrink0r\PhpSchema\Exception
     * @expectedExceptionMessage Class 'Shrink0r\PhpSchema\Tests\Void' that has been registered for type 'scalar' does not exist
     */
    public function testCreateWithNonExistingClass()
    {
        new Factory([ 'scalar' => Void::class ]);
    } // @codeCoverageIgnore

    /**
     * @expectedException \Shrink0r\PhpSchema\Exception
     * @expectedExceptionMessage Missing required key 'type' within property definition.
     */
    public function testCreatePropertyWithMissingType()
    {
        $factory = new Factory();
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();
        $factory->createProperty([ 'name' => 'foo' ], $mockSchema);
    } // @codeCoverageIgnore

    /**
     * @expectedException \Shrink0r\PhpSchema\Exception
     * @expectedExceptionMessage Missing required key 'name' within property definition.
     */
    public function testCreatePropertyWithMissingName()
    {
        $factory = new Factory();
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();
        $factory->createProperty([ 'type' => 'scalar' ], $mockSchema);
    } // @codeCoverageIgnore

    /**
     * @expectedException \Shrink0r\PhpSchema\Exception
     * @expectedExceptionMessage Given property type 'foo' has not been registered.
     */
    public function testCreatePropertyWithNonRegisteredType()
    {
        $factory = new Factory();
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();
        $factory->createProperty([ 'type' => 'foo', 'name' => 'bar' ], $mockSchema);
    } // @codeCoverageIgnore
}
