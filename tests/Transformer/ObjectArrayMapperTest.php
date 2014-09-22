<?php

namespace Tests\BlackBox\Transformer;

use BlackBox\Adapter\ArrayStorage;
use BlackBox\Transformer\ObjectArrayMapper;

/**
 * @covers \BlackBox\Transformer\ObjectArrayMapper
 */
class ObjectArrayMapperTest extends \PHPUnit_Framework_TestCase
{
    const FIXTURE = 'Tests\BlackBox\Transformer\FixtureClass';

    /**
     * @test
     */
    public function it_should_map_object_to_array()
    {
        $wrapped = new ArrayStorage();
        $storage = new ObjectArrayMapper($wrapped, self::FIXTURE);

        $object = new FixtureClass('string', 123, ['array']);

        $storage->set('foo', $object);

        $expectedArray = [
            'public'    => 'string',
            'protected' => 123,
            'private'   => ['array'],
        ];
        $this->assertEquals($expectedArray, $wrapped->get('foo'));
    }

    /**
     * @test
     */
    public function it_should_map_array_to_object()
    {
        $array = [
            'public'    => 'string',
            'protected' => 123,
            'private'   => ['array'],
        ];

        $wrapped = new ArrayStorage();
        $wrapped['foo'] = $array;

        $storage = new ObjectArrayMapper($wrapped, self::FIXTURE);

        $expectedObject = new FixtureClass('string', 123, ['array']);
        $this->assertEquals($expectedObject, $storage->get('foo'));
    }

    /**
     * @test
     */
    public function it_should_passthrough_primitive_types()
    {
        $wrapped = new ArrayStorage();
        $storage = new ObjectArrayMapper($wrapped, self::FIXTURE);

        $storage->set('foo', 123);

        $this->assertEquals(123, $wrapped->get('foo'));
        $this->assertEquals(123, $storage->get('foo'));
    }

    /**
     * @test
     */
    public function it_should_ignore_static_properties()
    {
        $wrapped = new ArrayStorage();
        $storage = new ObjectArrayMapper($wrapped, self::FIXTURE);

        $storage->set('foo', new FixtureClass(null, null, null));

        $array = $wrapped->get('foo');

        $this->assertNotContains('static', $array);
    }

    /**
     * @test
     */
    public function it_should_ignore_unknown_properties()
    {
        $array = [
            'foo' => 'bar',
            'baz' => 123,
        ];

        $wrapped = new ArrayStorage();
        $wrapped['foo'] = $array;

        $storage = new ObjectArrayMapper($wrapped, self::FIXTURE);

        $expectedObject = new FixtureClass(null, null, null);
        $this->assertEquals($expectedObject, $storage->get('foo'));
    }
}

class FixtureClass
{
    public $public;
    protected $protected;
    private $private;

    public static $static = 'hello';

    public function __construct($public, $protected, $private)
    {
        $this->public = $public;
        $this->protected = $protected;
        $this->private = $private;
    }
}
