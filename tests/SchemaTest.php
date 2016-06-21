<?php

namespace Shrink0r\Configr\Tests;

use PHPUnit_Framework_TestCase;
use Shrink0r\Configr\Error;
use Shrink0r\Configr\Exception;
use Shrink0r\Configr\Schema;

class SchemaTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideValidateFixtures
     */
    public function testValidate(array $givenSchema, array $givenData, array $expectedErrors)
    {
        $schema = new Schema('command_bus', $givenSchema);
        $result = $schema->validate($givenData);

        $this->assertInstanceOf(Error::class, $result);
        $this->assertEquals($expectedErrors, $result->unwrap());
    }

    public function testGetters()
    {
        $schema = new Schema('address', [
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
        ]);

        $this->assertCount(3, $schema->getProperties());
        $this->assertEquals('assoc', $schema->getType());
    }

    public function testInvalidPropertyType()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Unsupported property-type 'foo' given.");

        new Schema('address', [
            'type' => 'assoc',
            'properties' => [
                'street' => [ 'type' => 'foo' ]
            ]
        ]);
    } // @codeCoverageIgnore

    public function testMissingPropertiesKey()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Missing valid value for 'properties' key within given schema.");

        new Schema('address', [ 'type' => 'assoc' ]);
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
