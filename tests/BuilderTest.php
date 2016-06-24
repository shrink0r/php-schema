<?php

namespace Shrink0r\Configr\Tests;

use PHPUnit_Framework_TestCase;
use Shrink0r\Configr\Builder;
use Shrink0r\Configr\Error;
use Shrink0r\Configr\Ok;
use Shrink0r\Configr\SchemaInterface;

class BuilderTest extends PHPUnit_Framework_TestCase
{
    public function testBuildWithoutSchema()
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

    public function testBuildOk()
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

    public function testBuildError()
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

    public function testOffsetSetAndGet()
    {
        $builder = new Builder;
        $builder->foo->bar = 'foobar!';
        $builder['foo']['greetings'] = 'hello world!';

        $this->assertEquals('foobar!', $builder->foo->valueOf('bar'));
        $this->assertEquals('foobar!', $builder['foo']->valueOf('bar'));
    }

    public function testOffsetExistsAndUnset()
    {
        $builder = new Builder;
        $builder->foo->bar = 'foobar!';
        $builder['foo']['greetings'] = 'hello world!';
        if (isset($builder['foo']['greetings'])) {
            unset($builder['foo']['greetings']);
        }
        $this->assertEquals('foobar!', $builder['foo']->valueOf('bar'));
        $this->assertNull($builder['foo']->valueOf('greetings'));
        $result = $builder->build();
        $this->assertInstanceOf(Ok::class, $result);
        $this->assertEquals([ 'foo' => [ 'bar' => 'foobar!' ] ], $result->unwrap());

        if (isset($builder->foo)) {
            unset($builder->foo);
        }
        $result = $builder->build();
        $this->assertInstanceOf(Ok::class, $result);
        $this->assertEquals([], $result->unwrap());
    }

    public function testEnd()
    {
        $builder = new Builder;
        $builder
            ->foo
                ->bar
                    ->greetings('hello world!')
                ->end()
            ->end();

        $this->assertEquals('hello world!', $builder->foo->bar->valueOf('greetings'));
        // the BuilderStack should not not allow to drop the root builder.
        $this->assertEquals('hello world!', $builder->end()->end()->foo->bar->valueOf('greetings'));

        $builder = new Builder;
        $builder = $builder
            ->foo
                ->bar
                    ->greetings('hello world!')
                ->end();

        $this->assertEquals('hello world!', $builder->bar->valueOf('greetings'));
    }

    public function testRewind()
    {
        $builder = new Builder;
        $stack = $builder
            ->end()
            ->rewind()
            ->foo
                ->bar
                    ->message("hello world!")
            ->rewind()
            ->foo
                ->bar;

        $this->assertEquals('hello world!', $stack->valueOf('message'));
        $this->assertEquals('hello world!', $stack->rewind()['foo']['bar']->valueOf('message'));
    }

    public function testEndNesting() {
        $builder = new Builder();
        $result = $builder
            ->firstLevel1
                ->secondLevel1
                    ->key1('value1')
                ->end()
                ->key2('value2')
                ->secondLevel2
                    ->key3('value3')
                ->end()
            ->end()
            ->firstLevel2
                ->key4('value4')
            ->end()
            ->key5('value5')
            ->build()
        ;

        $expectedData = [
            'firstLevel1' => [
                'secondLevel1' => [
                    'key1' => 'value1',
                ],
                'key2' => 'value2',
                'secondLevel2' => [
                    'key3' => 'value3',
                ],
            ],
            'firstLevel2' => [
                'key4' => 'value4',
            ],
            'key5' => 'value5',
        ];

        $this->assertInstanceOf(Ok::class, $result);
        $this->assertEquals($expectedData, $result->unwrap());
    }

    public function testCanAccessArrayAfterInsert()
    {
        $builder = new Builder();
        $result = $builder
            ->foo([
                'bar' => 'value'
            ])
            ->foo
                ->bar('newValue')
            ->build()
        ;
        $expectedData = [
            'foo' => [
                'bar' => 'newValue'
            ]
        ];

        $this->assertInstanceOf(Ok::class, $result);
        $this->assertEquals($expectedData, $result->unwrap());
    }

    /**
     * @expectedException Shrink0r\Configr\Exception
     */
    public function testTypeDivergenceArrayToScalarThrows()
    {
        $builder = new Builder();
        $builder
            ->foo
                ->bar('value')
            ->end()
            ->foo('value')
        ;
    }

    /**
     * @expectedException Shrink0r\Configr\Exception
     */
    public function testTypeDivergenceScalarToArrayThrows()
    {
        $builder = new Builder();
        $builder
            ->foo('value')
            ->foo
                ->bar('value')
        ;
    }

    public function testBuilderNestingInValue()
    {
        $builder = new Builder();
        $result = $builder
            ->foo(
                (new Builder)
                    ->bar('barValue')
                    ->baz('bazValue')
            )
            ->foo
                ->baz('newBazValue')
            ->build()
        ;

        $expectedData = [
            'foo' => [
                'bar' => 'barValue',
                'baz' => 'newBazValue',
            ]
        ];

        $this->assertInstanceOf(Ok::class, $result);
        $this->assertEquals($expectedData, $result->unwrap());
    }

    public function testInvalidBuilderAsValue()
    {
        $builder = new Builder();
    }
}
