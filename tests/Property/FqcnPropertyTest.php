<?php

namespace Shrink0r\Configr\Tests\Property;

use PHPUnit_Framework_TestCase;
use Shrink0r\Configr\Error;
use Shrink0r\Configr\Ok;
use Shrink0r\Configr\Property\FqcnProperty;
use Shrink0r\Configr\SchemaInterface;

class FqcnPropertyTest extends PHPUnit_Framework_TestCase
{
    public function testValidateOk()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();

        $property = new FqcnProperty($mockSchema, 'class', [ 'required' => true ]);
        $result = $property->validate([ 'class' => SchemaInterface::class ]);

        $this->assertInstanceOf(Ok::class, $result);
    }

    public function testValidateError()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();

        $property = new FqcnProperty($mockSchema, 'class', [ 'required' => true ]);
        $result = $property->validate([ 'class' => Foobar::class ]);
        $expectedErrors = [ Error::CLASS_NOT_EXISTS ];

        $this->assertInstanceOf(Error::class, $result);
        $this->assertEquals($expectedErrors, $result->unwrap());
    }
}
