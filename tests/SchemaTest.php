<?php

namespace Shrink0r\Configr\Tests;

use PHPUnit_Framework_TestCase;
use Shrink0r\Configr\Error;
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
