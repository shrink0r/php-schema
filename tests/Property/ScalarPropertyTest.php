<?php

namespace Shrink0r\Configr\Tests\Property;

use PHPUnit_Framework_TestCase;
use Shrink0r\Configr\Error;
use Shrink0r\Configr\Ok;
use Shrink0r\Configr\Property\ScalarProperty;
use Shrink0r\Configr\SchemaInterface;

class ScalarPropertyTest extends PHPUnit_Framework_TestCase
{
    public function testValidateOk()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();

        $property = new ScalarProperty($mockSchema, 'value', [ 'required' => true ]);
        $result = $property->validate([ 'value' => 23 ]);

        $this->assertInstanceOf(Ok::class, $result);
    }

    public function testValidateError()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();

        $property = new ScalarProperty($mockSchema, 'value', [ 'required' => true ]);
        $result = $property->validate([ 'value' => [ 'foo' => 'bar' ] ]);
        $expectedErrors = [ Error::NON_SCALAR ];

        $this->assertInstanceOf(Error::class, $result);
        $this->assertEquals($expectedErrors, $result->unwrap());
    }
}
