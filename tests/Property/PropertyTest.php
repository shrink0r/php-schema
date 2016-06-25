<?php

namespace Shrink0r\PhpSchema\Tests\Property;

use PHPUnit_Framework_TestCase;
use Shrink0r\PhpSchema\Ok;
use Shrink0r\PhpSchema\Property\Property;
use Shrink0r\PhpSchema\SchemaInterface;

class PropertyTest extends PHPUnit_Framework_TestCase
{
    public function testGetters()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();
        $property = new Property($mockSchema, 'testProperty', [ 'required' => true ]);

        $this->assertEquals('testProperty', $property->getName());
        $this->assertNull($property->getParent());
        $this->assertFalse($property->hasParent());
        $this->assertEquals($mockSchema, $property->getSchema());
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
        $result = $property->validate('foobar');

        $this->assertInstanceOf(Ok::class, $result);
    }
}
