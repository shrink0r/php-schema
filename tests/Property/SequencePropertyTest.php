<?php

namespace Shrink0r\Configr\Tests\Property;

use PHPUnit_Framework_TestCase;
use Shrink0r\Configr\Error;
use Shrink0r\Configr\Ok;
use Shrink0r\Configr\Property\SequenceProperty;
use Shrink0r\Configr\SchemaInterface;

class SequencePropertyTest extends PHPUnit_Framework_TestCase
{
    public function testValidateOk()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();

        $property = new SequenceProperty($mockSchema, 'seq-value', [ 'required' => true, 'one_of' => [ 'scalar' ] ]);
        $result = $property->validate([ 'seq-value' => [ 23, 42, 5 ] ]);

        $this->assertInstanceOf(Ok::class, $result);
    }

    public function testValidateError()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();

        $property = new SequenceProperty($mockSchema, 'seq-value', [ 'required' => true, 'one_of' => [ 'fqcn' ] ]);
        $result = $property->validate([ 'seq-value' => [ SequenceProperty::class, TheVoid::class ] ]);
        $expectedErrors = [ 1 => [ Error::CLASS_NOT_EXISTS ] ];

        $this->assertInstanceOf(Error::class, $result);
        $this->assertEquals($expectedErrors, $result->unwrap());
    }
}
