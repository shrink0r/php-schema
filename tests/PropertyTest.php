<?php

namespace Shrink0r\Configr\Tests;

use Shrink0r\Configr\Property;
use Shrink0r\Configr\SchemaInterface;
use PHPUnit_Framework_TestCase;

class PropertyTest extends PHPUnit_Framework_TestCase
{
    public function testGetName()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();
        $property = new Property($mockSchema, 'testProperty', [ 'required' => true ]);

        $this->assertEquals('testProperty', $property->getName());
    }

    public function testIsRequired()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();

        $property = new Property($mockSchema, 'testProperty', [ 'required' => true ]);
        $this->assertTrue($property->isRequired());

        $property = new Property($mockSchema, 'testProperty', [ 'required' => false ]);
        $this->assertFalse($property->isRequired());
    }

    public function testValidate()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();
        $property = new Property($mockSchema, 'testProperty', [ 'required' => true ]);

        $this->assertEmpty($property->validate([ 'testProperty' => 'foobar' ]));
    }
}
