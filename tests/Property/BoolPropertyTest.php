<?php

namespace Shrink0r\PhpSchema\Tests\Property;

use PHPUnit_Framework_TestCase;
use Shrink0r\PhpSchema\Error;
use Shrink0r\PhpSchema\Ok;
use Shrink0r\PhpSchema\Property\BoolProperty;
use Shrink0r\PhpSchema\SchemaInterface;

class BoolPropertyTest extends PHPUnit_Framework_TestCase
{
    public function testValidateOk()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();

        $property = new BoolProperty($mockSchema, 'value', [ 'required' => true ]);
        $result = $property->validate(false);

        $this->assertInstanceOf(Ok::class, $result);
    }

    public function testValidateError()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();

        $property = new BoolProperty($mockSchema, 'value', [ 'required' => true ]);
        $result = $property->validate('boo!');
        $expectedErrors = [ Error::NON_BOOL ];

        $this->assertInstanceOf(Error::class, $result);
        $this->assertEquals($expectedErrors, $result->unwrap());
    }
}
