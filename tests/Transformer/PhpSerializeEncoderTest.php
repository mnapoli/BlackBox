<?php

namespace Tests\BlackBox\Transformer;

use BlackBox\Transformer\PhpSerializeEncoder;

/**
 * @covers \BlackBox\Transformer\PhpSerializeEncoder
 */
class PhpSerializeEncoderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PhpSerializeEncoder
     */
    private $transformer;

    public function setUp()
    {
        $this->transformer = new PhpSerializeEncoder();
    }

    /**
     * @test
     */
    public function it_should_serialize_data()
    {
        $data = ['bar', 123, new \stdClass()];

        $transformed = $this->transformer->transform($data);

        $this->assertEquals(serialize($data), $transformed);
    }

    /**
     * @test
     */
    public function it_should_unserialize_data()
    {
        $data = ['bar', 123, new \stdClass()];

        $decoded = $this->transformer->reverseTransform(serialize($data));

        $this->assertEquals($data, $decoded);
    }

    /**
     * @test
     */
    public function it_should_handle_get_null()
    {
        $this->assertNull($this->transformer->reverseTransform(null));
    }
}
