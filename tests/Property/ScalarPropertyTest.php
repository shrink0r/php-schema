<?php

namespace Shrink0r\PhpSchema\Tests\Property;

use PHPUnit_Framework_TestCase;
use Shrink0r\PhpSchema\Error;
use Shrink0r\PhpSchema\Ok;
use Shrink0r\PhpSchema\Property\ScalarProperty;
use Shrink0r\PhpSchema\SchemaInterface;

class ScalarPropertyTest extends PHPUnit_Framework_TestCase
{
    public function testValidateOk()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();

        $property = new ScalarProperty($mockSchema, 'value', [ 'required' => true ]);
        $result = $property->validate(23);

        $this->assertInstanceOf(Ok::class, $result);
    }

    public function testValidateError()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();

        $property = new ScalarProperty($mockSchema, 'value', [ 'required' => true ]);
        $result = $property->validate([ 'foo' => 'bar' ]);
        $expectedErrors = [ Error::NON_SCALAR ];

        $this->assertInstanceOf(Error::class, $result);
        $this->assertEquals($expectedErrors, $result->unwrap());
    }
}
