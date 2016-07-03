<?php

namespace Shrink0r\PhpSchema\Tests\Property;

use PHPUnit_Framework_TestCase;
use Shrink0r\PhpSchema\Error;
use Shrink0r\PhpSchema\Ok;
use Shrink0r\PhpSchema\Factory;
use Shrink0r\PhpSchema\Property\SequenceProperty;
use Shrink0r\PhpSchema\SchemaInterface;

class SequencePropertyTest extends PHPUnit_Framework_TestCase
{
    public function testValidateOk()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();
        $mockSchema->method('getFactory')
             ->willReturn(new Factory());

        $property = new SequenceProperty(
            $mockSchema,
            'value',
            [
                'required' => true,
                'one_of' => [ 'int', 'string', 'float', 'bool' ]
            ]
        );
        $result = $property->validate([ true, 23, 42.0, 'foo' ]);

        $this->assertInstanceOf(Ok::class, $result);
    }

    public function testValidateError()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();
        $mockSchema->method('getFactory')
             ->willReturn(new Factory());

        $property = new SequenceProperty($mockSchema, 'value', [ 'required' => true, 'one_of' => [ 'fqcn' ] ]);
        $result = $property->validate([ SequenceProperty::class, TheVoid::class ]);
        $expectedErrors = [ 1 => [ Error::CLASS_NOT_EXISTS ] ];

        $this->assertInstanceOf(Error::class, $result);
        $this->assertEquals($expectedErrors, $result->unwrap());
    }

    public function testInvalidValue()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();
        $mockSchema->method('getFactory')
             ->willReturn(new Factory());

        $property = new SequenceProperty($mockSchema, 'value', [ 'required' => true, 'one_of' => [ 'fqcn' ] ]);
        $result = $property->validate('meh');
        $expectedErrors = [ Error::NON_ARRAY ];

        $this->assertInstanceOf(Error::class, $result);
        $this->assertEquals($expectedErrors, $result->unwrap());
    }

    public function testValidateAnyOk()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();
        $mockSchema->method('getFactory')
             ->willReturn(new Factory());

        $property = new SequenceProperty($mockSchema, 'value', [ 'required' => true, 'one_of' => [ 'any' ] ]);
        $result = $property->validate([ 23, 'foobar', [ 'foo', 'bar' ] ]);

        $this->assertInstanceOf(Ok::class, $result);
    }

    /**
     * @expectedException \Shrink0r\PhpSchema\Exception
     * @expectedExceptionMessage Unable to resolve 'moep' to a custom type-definition.
     */
    public function testInvalidCustomType()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();
        $mockSchema->method('getFactory')
             ->willReturn(new Factory());

        $property = new SequenceProperty($mockSchema, 'value', [ 'required' => true, 'one_of' => [ '&moep' ] ]);
        $property->validate([ 23 ]);
    } // @codeCoverageIgnore

    /**
     * @expectedException \Shrink0r\PhpSchema\Exception
     * @expectedExceptionMessage Given property type 'moep' has not been registered.
     */
    public function testInvalidPropertyType()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();
        $mockSchema->method('getFactory')
             ->willReturn(new Factory());

        $property = new SequenceProperty($mockSchema, 'value', [ 'required' => true, 'one_of' => [ 'moep' ] ]);
        $property->validate([ 23 ]);
    } // @codeCoverageIgnore
}
