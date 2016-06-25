<?php

namespace Shrink0r\PhpSchema\Tests\Property;

use PHPUnit_Framework_TestCase;
use Shrink0r\PhpSchema\Error;
use Shrink0r\PhpSchema\Ok;
use Shrink0r\PhpSchema\Property\FloatProperty;
use Shrink0r\PhpSchema\SchemaInterface;

class FloatPropertyTest extends PHPUnit_Framework_TestCase
{
    public function testValidateOk()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();

        $property = new FloatProperty($mockSchema, 'value', [ 'required' => true ]);
        $result = $property->validate(12.0);

        $this->assertInstanceOf(Ok::class, $result);
    }

    public function testValidateError()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();

        $property = new FloatProperty($mockSchema, 'value', [ 'required' => true ]);
        $result = $property->validate(2);
        $expectedErrors = [ Error::NON_FLOAT ];

        $this->assertInstanceOf(Error::class, $result);
        $this->assertEquals($expectedErrors, $result->unwrap());
    }
}
