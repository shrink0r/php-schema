<?php

namespace Shrink0r\PhpSchema\Tests\Property;

use PHPUnit_Framework_TestCase;
use Shrink0r\PhpSchema\Error;
use Shrink0r\PhpSchema\Ok;
use Shrink0r\PhpSchema\Property\ChoiceProperty;
use Shrink0r\PhpSchema\SchemaInterface;

class ChoicePropertyTest extends PHPUnit_Framework_TestCase
{
    public function testValidateOk()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();

        $property = new ChoiceProperty($mockSchema, 'value', [ 'required' => true, 'one_of' => [ 'foo', 'bar' ] ]);
        $result = $property->validate('foo');

        $this->assertInstanceOf(Ok::class, $result);
    }

    public function testValidateError()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();

        $property = new ChoiceProperty($mockSchema, 'value', [ 'required' => true, 'one_of' => [ 'foo', 'bar' ] ]);
        $result = $property->validate('boo!');
        $expectedErrors = [ Error::INVALID_CHOICE ];

        $this->assertInstanceOf(Error::class, $result);
        $this->assertEquals($expectedErrors, $result->unwrap());
    }
}
