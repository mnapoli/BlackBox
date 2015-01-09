<?php

namespace Tests\BlackBox\Transformer;

use BlackBox\Transformer\JmsSerializer;
use JMS\Serializer\SerializerBuilder;
use Tests\BlackBox\Transformer\JmsSerializer\FixtureClass;

/**
 * @covers \BlackBox\Transformer\JmsSerializer
 */
class JmsSerializerTest extends \PHPUnit_Framework_TestCase
{
    const FIXTURE = 'Tests\BlackBox\Transformer\JmsSerializer\FixtureClass';

    /**
     * @var JmsSerializer
     */
    private $transformer;

    public function setUp()
    {
        parent::setUp();

        $jmsSerializer = SerializerBuilder::create()
            ->addMetadataDir(__DIR__ . '/JmsSerializer')
            ->build();

        $this->transformer = new JmsSerializer($jmsSerializer, 'json', self::FIXTURE);
    }

    /**
     * @test
     */
    public function it_should_serialize_objects()
    {
        $object = new FixtureClass('hello');
        $expected = '{"property":"hello"}';

        $this->assertEquals($expected, $this->transformer->transform($object));
    }

    /**
     * @test
     */
    public function it_should_deserialize_objects()
    {
        $json = '{"property":"hello"}';
        $expected = new FixtureClass('hello');

        $this->assertEquals($expected, $this->transformer->reverseTransform($json));
    }

    /**
     * @test
     */
    public function it_should_passthrough_null()
    {
        $this->assertSame(null, $this->transformer->reverseTransform(null));
    }
}
