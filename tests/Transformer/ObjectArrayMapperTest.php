<?php

namespace Tests\BlackBox\Transformer;

use BlackBox\Backend\ArrayStorage;
use BlackBox\Transformer\ObjectArrayMapper;
use Tests\BlackBox\Transformer\ObjectArrayMapper\FixtureClass;

/**
 * @covers \BlackBox\Transformer\ObjectArrayMapper
 */
class ObjectArrayMapperTest extends \PHPUnit_Framework_TestCase
{
    const FIXTURE = 'Tests\BlackBox\Transformer\ObjectArrayMapper\FixtureClass';

    /**
     * @var ObjectArrayMapper
     */
    private $transformer;

    /**
     * @var ArrayStorage
     */
    private $storage;

    public function setUp()
    {
        $this->storage = new ArrayStorage;
        $this->transformer = new ObjectArrayMapper($this->storage, self::FIXTURE);
    }

    /**
     * @test
     */
    public function it_should_encode_object_to_array()
    {
        $object = new FixtureClass('string', 123, ['array']);

        $this->transformer->set('foo', $object);

        $expectedArray = [
            'public' => 'string',
            'protected' => 123,
            'private' => ['array'],
        ];
        $this->assertEquals($expectedArray, $this->storage->get('foo'));
    }

    /**
     * @test
     */
    public function it_should_decode_array_to_object()
    {
        $object = new FixtureClass('string', 123, ['array']);

        $this->transformer->set('foo', $object);

        $this->assertEquals($object, $this->transformer->get('foo'));
        $this->assertNotSame($object, $this->transformer->get('foo'));
    }

    /**
     * @test
     */
    public function it_should_support_primitive_types()
    {
        $this->transformer->set('foo', 123);

        $this->assertEquals(123, $this->transformer->get('foo'));
    }

    /**
     * @test
     */
    public function it_should_ignore_static_properties()
    {
        $object = new FixtureClass(null, null, null);

        $this->transformer->set('foo', $object);

        $this->assertNotContains('static', $this->storage->get('foo'));
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
        $this->storage->set('foo', $array);

        $expectedObject = new FixtureClass(null, null, null);
        $this->assertEquals($expectedObject, $this->transformer->get('foo'));
    }
}
