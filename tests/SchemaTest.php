<?php

namespace Shrink0r\PhpSchema\Tests;

use PHPUnit_Framework_TestCase;
use Shrink0r\PhpSchema\Error;
use Shrink0r\PhpSchema\Exception;
use Shrink0r\PhpSchema\Factory;
use Shrink0r\PhpSchema\Schema;

class SchemaTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideValidateFixtures
     */
    public function testValidate(array $givenSchema, array $givenData, array $expectedErrors)
    {
        $schema = new Schema('command_bus', $givenSchema, new Factory());
        $result = $schema->validate($givenData);

        $this->assertInstanceOf(Error::class, $result);
        $this->assertEquals($expectedErrors, $result->unwrap());
    }

    public function testGetters()
    {
        $schema = new Schema(
            'address',
            [
                'type' => 'assoc',
                'properties' => [
                    'street' => [ 'type' => 'string' ],
                    'zipcode' => [ 'type' => 'string' ],
                    'coords' => [
                        'type' => 'assoc',
                        'properties' => [
                            'lon' => [ 'type' => 'float' ],
                            'lat' => [ 'type' => 'float' ]
                        ]
                    ]
                ]
            ],
            new Factory()
        );

        $this->assertCount(3, $schema->getProperties());
        $this->assertEquals('address', $schema->getName());
        $this->assertEquals('assoc', $schema->getType());
    }

    /**
     * @expectedException Shrink0r\PhpSchema\Exception
     * @expectedExceptionMessage Given property type 'foo' has not been registered.
     */
    public function testInvalidPropertyType()
    {
        new Schema(
            'address',
            [
                'type' => 'assoc',
                'properties' => [
                    'street' => [ 'type' => 'foo' ]
                ]
            ],
            new Factory()
        );
    } // @codeCoverageIgnore

    /**
     * @expectedException Shrink0r\PhpSchema\Exception
     * @expectedExceptionMessage Given value for key 'customTypes' is not an array.
     */
    public function testInvalidCustomTypes()
    {
        new Schema(
            'address',
            [
                'type' => 'assoc',
                'properties' => [ 'street' => [ 'type' => 'string' ] ],
                'customTypes' => 'foobar'
            ],
            new Factory()
        );
    } // @codeCoverageIgnore

    /**
     * @expectedException Shrink0r\PhpSchema\Exception
     * @expectedExceptionMessage Missing valid value for 'properties' key within given schema.
     */
    public function testMissingPropertiesKey()
    {
        new Schema('address', [ 'type' => 'assoc' ], new Factory());
    } // @codeCoverageIgnore

    /**
     * @codeCoverageIgnore
     */
    public function provideValidateFixtures()
    {
        return [
            'schema-1' => require __DIR__.'/Fixture/SchemaTest_001.php',
            'schema-2' => require __DIR__.'/Fixture/SchemaTest_002.php',
            'schema-3' => require __DIR__.'/Fixture/SchemaTest_003.php'
        ];
    }
}
