<?php

namespace Shrink0r\Configr\Tests\Property;

use PHPUnit_Framework_TestCase;
use Shrink0r\Configr\Error;
use Shrink0r\Configr\Exception;
use Shrink0r\Configr\Ok;
use Shrink0r\Configr\Factory;
use Shrink0r\Configr\Property\EnumProperty;
use Shrink0r\Configr\SchemaInterface;

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

    public function testInvalidCustomType()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Unable to resolve 'moep' to a custom type-definition.");

        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();
        $mockSchema->method('getFactory')
             ->willReturn(new Factory());

        $property = new EnumProperty($mockSchema, 'value', [ 'required' => true, 'one_of' => [ '&moep' ] ]);
        $property->validate(23);
    } // @codeCoverageIgnore

    public function testInvalidPropertyType()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Given property type 'moep' has not been registered.");

        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();
        $mockSchema->method('getFactory')
             ->willReturn(new Factory());

        $property = new EnumProperty($mockSchema, 'value', [ 'required' => true, 'one_of' => [ 'moep' ] ]);
        $property->validate(23);
    } // @codeCoverageIgnore
}
