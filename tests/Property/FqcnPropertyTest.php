<?php

namespace Shrink0r\PhpSchema\Tests\Property;

use PHPUnit_Framework_TestCase;
use Shrink0r\PhpSchema\Error;
use Shrink0r\PhpSchema\Ok;
use Shrink0r\PhpSchema\Property\FqcnProperty;
use Shrink0r\PhpSchema\SchemaInterface;

class FqcnPropertyTest extends PHPUnit_Framework_TestCase
{
    public function testValidateOk()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();

        $property = new FqcnProperty($mockSchema, 'class', [ 'required' => true ]);
        $result = $property->validate(SchemaInterface::class);

        $this->assertInstanceOf(Ok::class, $result);
    }

    public function testValidateError()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();

        $property = new FqcnProperty($mockSchema, 'class', [ 'required' => true ]);
        $result = $property->validate(Foobar::class);
        $expectedErrors = [ Error::CLASS_NOT_EXISTS ];

        $this->assertInstanceOf(Error::class, $result);
        $this->assertEquals($expectedErrors, $result->unwrap());
    }
}
