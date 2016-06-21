<?php

namespace Shrink0r\Configr\Tests\Property;

use PHPUnit_Framework_TestCase;
use Shrink0r\Configr\Error;
use Shrink0r\Configr\Ok;
use Shrink0r\Configr\Property\StringProperty;
use Shrink0r\Configr\SchemaInterface;

class StringPropertyTest extends PHPUnit_Framework_TestCase
{
    public function testValidateOk()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();

        $property = new StringProperty($mockSchema, 'value', [ 'required' => true ]);
        $result = $property->validate([ 'value' => 'hello world!' ]);

        $this->assertInstanceOf(Ok::class, $result);
    }

    public function testValidateError()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();

        $property = new StringProperty($mockSchema, 'value', [ 'required' => true ]);
        $result = $property->validate([ 'value' => 23 ]);
        $expectedErrors = [ Error::NON_STRING ];

        $this->assertInstanceOf(Error::class, $result);
        $this->assertEquals($expectedErrors, $result->unwrap());
    }
}
