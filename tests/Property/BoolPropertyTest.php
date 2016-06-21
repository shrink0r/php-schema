<?php

namespace Shrink0r\Configr\Tests\Property;

use PHPUnit_Framework_TestCase;
use Shrink0r\Configr\Error;
use Shrink0r\Configr\Ok;
use Shrink0r\Configr\Property\BoolProperty;
use Shrink0r\Configr\SchemaInterface;

class BoolPropertyTest extends PHPUnit_Framework_TestCase
{
    public function testValidateOk()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();

        $property = new BoolProperty($mockSchema, 'value', [ 'required' => true ]);
        $result = $property->validate([ 'value' => false ]);

        $this->assertInstanceOf(Ok::class, $result);
    }

    public function testValidateError()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();

        $property = new BoolProperty($mockSchema, 'value', [ 'required' => true ]);
        $result = $property->validate([ 'value' => 'boo!' ]);
        $expectedErrors = [ Error::NON_BOOL ];

        $this->assertInstanceOf(Error::class, $result);
        $this->assertEquals($expectedErrors, $result->unwrap());
    }
}
