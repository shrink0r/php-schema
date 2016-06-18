<?php

namespace Shrink0r\Configr\Tests;

use PHPUnit_Framework_TestCase;
use Shrink0r\Configr\Schema;

class SchemaTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideValidateFixtures
     */
    public function testValidate(array $givenSchema, array $givenConfig, array $expectedErrors)
    {
        $schema = new Schema('command_bus', $givenSchema);
        $resultingErrors = $schema->validate($givenConfig);

        $this->assertEquals($expectedErrors, $resultingErrors);
    }

    public function provideValidateFixtures()
    {
        return require __DIR__.'/Fixture/SchemaTest.php';
    }
}
