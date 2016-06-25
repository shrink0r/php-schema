<?php

namespace Shrink0r\PhpSchema\Tests\Property;

use PHPUnit_Framework_TestCase;
use Shrink0r\PhpSchema\Error;
use Shrink0r\PhpSchema\Ok;
use Shrink0r\PhpSchema\Property\IntProperty;
use Shrink0r\PhpSchema\SchemaInterface;

class IntPropertyTest extends PHPUnit_Framework_TestCase
{
    public function testValidateOk()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();

        $property = new IntProperty($mockSchema, 'value', [ 'required' => true ]);
        $result = $property->validate(42);

        $this->assertInstanceOf(Ok::class, $result);
    }

    public function testValidateError()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();

        $property = new IntProperty($mockSchema, 'value', [ 'required' => true ]);
        $result = $property->validate(2.3);
        $expectedErrors = [ Error::NON_INT ];

        $this->assertInstanceOf(Error::class, $result);
        $this->assertEquals($expectedErrors, $result->unwrap());
    }
}
