<?php

namespace Tests\BlackBox\Transformer;

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

    public function setUp()
    {
        $this->transformer = new ObjectArrayMapper(self::FIXTURE);
    }

    /**
     * @test
     */
    public function it_should_encode_object_to_array()
    {
        $object = new FixtureClass('string', 123, ['array']);
        $expectedArray = [
            'public'    => 'string',
            'protected' => 123,
            'private'   => ['array'],
        ];

        $this->assertEquals($expectedArray, $this->transformer->transform($object));
    }

    /**
     * @test
     */
    public function it_should_decode_array_to_object()
    {
        $array = [
            'public'    => 'string',
            'protected' => 123,
            'private'   => ['array'],
        ];
        $expectedObject = new FixtureClass('string', 123, ['array']);

        $this->assertEquals($expectedObject, $this->transformer->reverseTransform($array));
    }

    /**
     * @test
     */
    public function it_should_passthrough_primitive_types()
    {
        $data = $this->transformer->transform(123);

        $this->assertEquals(123, $data);
        $this->assertEquals(123, $this->transformer->reverseTransform($data));
    }

    /**
     * @test
     */
    public function it_should_ignore_static_properties()
    {
        $array = $this->transformer->transform(new FixtureClass(null, null, null));

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

        $expectedObject = new FixtureClass(null, null, null);
        $this->assertEquals($expectedObject, $this->transformer->reverseTransform($array));
    }
}
