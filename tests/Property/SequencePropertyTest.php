<?php

namespace Shrink0r\Configr\Tests\Property;

use PHPUnit_Framework_TestCase;
use Shrink0r\Configr\Error;
use Shrink0r\Configr\Exception;
use Shrink0r\Configr\Ok;
use Shrink0r\Configr\Property\SequenceProperty;
use Shrink0r\Configr\SchemaInterface;

class SequencePropertyTest extends PHPUnit_Framework_TestCase
{
    public function testValidateOk()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();

        $property = new SequenceProperty(
            $mockSchema,
            'value',
            [
                'required' => true,
                'one_of' => [ 'int', 'string', 'float', 'bool' ]
            ]
        );
        $result = $property->validate([ 'value' => [ true, 23, 42.0, 'foo' ] ]);

        $this->assertInstanceOf(Ok::class, $result);
    }

    public function testValidateError()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();

        $property = new SequenceProperty($mockSchema, 'value', [ 'required' => true, 'one_of' => [ 'fqcn' ] ]);
        $result = $property->validate([ 'value' => [ SequenceProperty::class, TheVoid::class ] ]);
        $expectedErrors = [ 1 => [ Error::CLASS_NOT_EXISTS ] ];

        $this->assertInstanceOf(Error::class, $result);
        $this->assertEquals($expectedErrors, $result->unwrap());
    }

    public function testInvalidValue()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();

        $property = new SequenceProperty($mockSchema, 'value', [ 'required' => true, 'one_of' => [ 'fqcn' ] ]);
        $result = $property->validate([ 'value' => 'meh' ]);
        $expectedErrors = [ Error::NON_ARRAY ];

        $this->assertInstanceOf(Error::class, $result);
        $this->assertEquals($expectedErrors, $result->unwrap());
    }

    public function testValidateAnyOk()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();

        $property = new SequenceProperty($mockSchema, 'value', [ 'required' => true, 'one_of' => [ 'any' ] ]);
        $result = $property->validate([ 'value' => [ 23, 'foobar', [ 'foo', 'bar' ] ] ]);

        $this->assertInstanceOf(Ok::class, $result);
    }

    public function testInvalidCustomType()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Unable to resolve 'moep' to a custom type-definition.");

        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();

        $property = new SequenceProperty($mockSchema, 'value', [ 'required' => true, 'one_of' => [ '&moep' ] ]);
        $property->validate([ 'value' => [ 23 ] ]);
    } // @codeCoverageIgnore

    public function testInvalidPropertyType()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Unsupported property-type 'moep' given.");

        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();

        $property = new SequenceProperty($mockSchema, 'value', [ 'required' => true, 'one_of' => [ 'moep' ] ]);
        $property->validate([ 'value' => [ 23 ] ]);
    } // @codeCoverageIgnore
}
