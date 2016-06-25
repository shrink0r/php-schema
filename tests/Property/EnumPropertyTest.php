<?php

namespace Shrink0r\PhpSchema\Tests\Property;

use PHPUnit_Framework_TestCase;
use Shrink0r\PhpSchema\Error;
use Shrink0r\PhpSchema\Exception;
use Shrink0r\PhpSchema\Ok;
use Shrink0r\PhpSchema\Factory;
use Shrink0r\PhpSchema\Property\EnumProperty;
use Shrink0r\PhpSchema\SchemaInterface;

class EnumPropertyTest extends PHPUnit_Framework_TestCase
{
    public function testValidateOk()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();
        $mockSchema->method('getFactory')
             ->willReturn(new Factory());

        $property = new EnumProperty(
            $mockSchema,
            'value',
            [
                'required' => true,
                'one_of' => [ 'int', 'string', 'float', 'bool' ]
            ]
        );
        $this->assertInstanceOf(Ok::class, $property->validate(23));
        $this->assertInstanceOf(Ok::class, $property->validate(2.3));
        $this->assertInstanceOf(Ok::class, $property->validate(true));
        $this->assertInstanceOf(Ok::class, $property->validate('hello world!'));

        $property = new EnumProperty(
            $mockSchema,
            'value',
            [ 'required' => true, 'one_of' => [ 'scalar' ] ]
        );
        $this->assertInstanceOf(Ok::class, $property->validate(23));
        $this->assertInstanceOf(Ok::class, $property->validate(2.3));
        $this->assertInstanceOf(Ok::class, $property->validate(true));
        $this->assertInstanceOf(Ok::class, $property->validate('hello world!'));
    }

    public function testValidateError()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();
        $mockSchema->method('getFactory')
             ->willReturn(new Factory());

        $property = new EnumProperty($mockSchema, 'value', [ 'required' => true, 'one_of' => [ 'fqcn' ] ]);
        $result = $property->validate(TheVoid::class);
        $expectedErrors = [ Error::CLASS_NOT_EXISTS ];

        $this->assertInstanceOf(Error::class, $result);
        $this->assertEquals($expectedErrors, $result->unwrap());
    }

    public function testValidateAnyOk()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();
        $mockSchema->method('getFactory')
             ->willReturn(new Factory());

        $property = new EnumProperty($mockSchema, 'value', [ 'required' => true, 'one_of' => [ 'any' ] ]);
        $result = $property->validate([ 'foo', [ 'foo' => 'bar' ] ]);

        $this->assertInstanceOf(Ok::class, $result);
    }

    /**
     * @expectedException Shrink0r\PhpSchema\Exception
     * @expectedExceptionMessage Unable to resolve 'moep' to a custom type-definition.
     */
    public function testInvalidCustomType()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();
        $mockSchema->method('getFactory')
             ->willReturn(new Factory());

        $property = new EnumProperty($mockSchema, 'value', [ 'required' => true, 'one_of' => [ '&moep' ] ]);
        $property->validate(23);
    } // @codeCoverageIgnore

    /**
     * @expectedException Shrink0r\PhpSchema\Exception
     * @expectedExceptionMessage Given property type 'moep' has not been registered.
     */
    public function testInvalidPropertyType()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();
        $mockSchema->method('getFactory')
             ->willReturn(new Factory());

        $property = new EnumProperty($mockSchema, 'value', [ 'required' => true, 'one_of' => [ 'moep' ] ]);
        $property->validate(23);
    } // @codeCoverageIgnore
}
