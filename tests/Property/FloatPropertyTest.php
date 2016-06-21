<?php

namespace Shrink0r\Configr\Tests\Property;

use PHPUnit_Framework_TestCase;
use Shrink0r\Configr\Error;
use Shrink0r\Configr\Ok;
use Shrink0r\Configr\Property\FloatProperty;
use Shrink0r\Configr\SchemaInterface;

class FloatPropertyTest extends PHPUnit_Framework_TestCase
{
    public function testValidateOk()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();

        $property = new FloatProperty($mockSchema, 'value', [ 'required' => true ]);
        $result = $property->validate([ 'value' => 12.0 ]);

        $this->assertInstanceOf(Ok::class, $result);
    }

    public function testValidateError()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();

        $property = new FloatProperty($mockSchema, 'value', [ 'required' => true ]);
        $result = $property->validate([ 'value' => 2 ]);
        $expectedErrors = [ Error::NON_FLOAT ];

        $this->assertInstanceOf(Error::class, $result);
        $this->assertEquals($expectedErrors, $result->unwrap());
    }
}
