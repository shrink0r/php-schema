<?php

namespace Shrink0r\Configr\Tests;

use PHPUnit_Framework_TestCase;
use Shrink0r\Configr\Builder;
use Shrink0r\Configr\Error;
use Shrink0r\Configr\Ok;
use Shrink0r\Configr\SchemaInterface;

class BuilderTest extends PHPUnit_Framework_TestCase
{
    public function testWithoutSchema()
    {
        $builder = new Builder;
        $result = $builder
            ->firstname('clark')
            ->lastname('kent')
            ->email('superuser@example.com')
            ->tags([ 'hero', 'security' ])
            ->address
                ->street('fleetstreet 23')
                ->zipCode('23542')
                ->city('melmac')
            ->build([
                'username' => 'superuser',
                'email' => 'clark.kent@example.com'
            ]);

        $expectedData = [
            'username' => 'superuser',
            'firstname' => 'clark',
            'lastname' => 'kent',
            'email' => 'superuser@example.com',
            'tags' => [ 'hero', 'security' ],
            'address' => [
                'street' => 'fleetstreet 23',
                'zipCode' => '23542',
                'city' => 'melmac'
            ]
        ];

        $this->assertInstanceOf(Ok::class, $result);
        $this->assertEquals($expectedData, $result->unwrap());
    }

    public function testWithSchemaOk()
    {
        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();
        $mockSchema->expects($this->once())
            ->method('validate')
            ->will($this->returnValue(Ok::unit()));

        $builder = new Builder($mockSchema);
        $result = $builder
            ->firstname('clark')
            ->lastname('kent')
            ->email('superuser@example.com')
            ->tags([ 'hero', 'security' ])
            ->address
                ->street('fleetstreet 23')
                ->zipCode('23542')
                ->city('melmac')
            ->build([
                'username' => 'superuser',
                'email' => 'clark.kent@example.com'
            ]);

        $expectedData = [
            'username' => 'superuser',
            'firstname' => 'clark',
            'lastname' => 'kent',
            'email' => 'superuser@example.com',
            'tags' => [ 'hero', 'security' ],
            'address' => [
                'street' => 'fleetstreet 23',
                'zipCode' => '23542',
                'city' => 'melmac'
            ]
        ];

        $this->assertInstanceOf(Ok::class, $result);
        $this->assertEquals($expectedData, $result->unwrap());
    }

    public function testWithSchemaError()
    {
        $expectedErrors = [ 'lastname' => 'missing_key' ];

        $mockSchema = $this->getMockBuilder(SchemaInterface::class)->getMock();
        $mockSchema->expects($this->once())
            ->method('validate')
            ->will($this->returnValue(Error::unit($expectedErrors)));

        $defaults = [ 'username' => 'superuser', 'email' => 'clark.kent@example.com' ];
        $builder = new Builder($mockSchema);
        $result = $builder->firstname('clark')->build($defaults);

        $this->assertInstanceOf(Error::class, $result);
        $this->assertEquals($expectedErrors, $result->unwrap());
    }
}
