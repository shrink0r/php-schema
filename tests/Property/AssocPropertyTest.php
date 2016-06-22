<?php

namespace Shrink0r\Configr\Tests\Property;

use PHPUnit_Framework_TestCase;
use Shrink0r\Configr\Error;
use Shrink0r\Configr\Ok;
use Shrink0r\Configr\Factory;
use Shrink0r\Configr\Property\AssocProperty;
use Shrink0r\Configr\SchemaInterface;

class AssocPropertyTest extends PHPUnit_Framework_TestCase
{
    public function testValidateOk()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();
        $mockSchema->method('getFactory')
             ->willReturn(new Factory());

        $definition = [
            'required' => true,
            'properties' => [
                'street' => [ 'type' => 'scalar' ],
                'zipcode' => [ 'type' => 'scalar' ],
                'coords' => [
                    'type' => 'assoc',
                    'properties' => [
                        'lon' => [ 'type' => 'scalar' ],
                        'lat' => [ 'type' => 'scalar' ]
                    ]
                ]
            ]
        ];

        $property = new AssocProperty($mockSchema, 'address', $definition);
        $result = $property->validate([
            'street' => 'fleet street 23',
            'zipcode' => '23542',
            'coords' => [
                'lon' => 12.65,
                'lat' => 13.54
            ]
        ]);

        $this->assertInstanceOf(Ok::class, $result);
    }

    public function testValidateError()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();
        $mockSchema->method('getFactory')
             ->willReturn(new Factory());

        $definition = [
            'required' => true,
            'properties' => [
                'street' => [ 'type' => 'scalar' ],
                'zipcode' => [ 'type' => 'scalar' ],
                'coords' => [
                    'type' => 'assoc',
                    'properties' => [
                        'lon' => [ 'type' => 'scalar' ],
                        'lat' => [ 'type' => 'scalar' ]
                    ]
                ]
            ]
        ];

        $property = new AssocProperty($mockSchema, 'address', $definition);
        $result = $property->validate([
            'street' => 'fleet street 23',
            'zipcode' => '23542',
            'coords' => 'foobar'
        ]);
        $expectedErrors = [ 'coords' => [ Error::NON_ARRAY ] ];

        $this->assertInstanceOf(Error::class, $result);
        $this->assertEquals($expectedErrors, $result->unwrap());
    }
}
