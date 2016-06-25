<?php

namespace Shrink0r\PhpSchema\Tests\Property;

use PHPUnit_Framework_TestCase;
use Shrink0r\PhpSchema\Error;
use Shrink0r\PhpSchema\Ok;
use Shrink0r\PhpSchema\Property\StringProperty;
use Shrink0r\PhpSchema\SchemaInterface;

class StringPropertyTest extends PHPUnit_Framework_TestCase
{
    public function testValidateOk()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();

        $property = new StringProperty($mockSchema, 'value', [ 'required' => true ]);
        $result = $property->validate('hello world!');

        $this->assertInstanceOf(Ok::class, $result);
    }

    public function testValidateError()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();

        $property = new StringProperty($mockSchema, 'value', [ 'required' => true ]);
        $result = $property->validate(23);
        $expectedErrors = [ Error::NON_STRING ];

        $this->assertInstanceOf(Error::class, $result);
        $this->assertEquals($expectedErrors, $result->unwrap());
    }
}
