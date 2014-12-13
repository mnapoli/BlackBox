<?php

namespace Tests\BlackBox\Transformer;

use BlackBox\Transformer\JsonEncoder;

/**
 * @covers \BlackBox\Transformer\JsonEncoder
 */
class JsonEncoderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_encode_into_json()
    {
        $transformer = new JsonEncoder();

        $data = ['bar', 123];

        $json = $transformer->transform($data);

        $this->assertEquals(json_encode($data), $json);
    }

    /**
     * @test
     */
    public function it_should_encode_into_json_pretty()
    {
        $transformer = new JsonEncoder(true);

        $data = ['bar', 123];

        $json = $transformer->transform($data);

        $this->assertEquals(json_encode($data, JSON_PRETTY_PRINT), $json);
    }

    /**
     * @test
     */
    public function it_should_decode_from_json()
    {
        $data = ['bar', 123];

        $json = json_encode($data);

        $transformer = new JsonEncoder();

        $this->assertEquals($data, $transformer->reverseTransform($json));
    }

    /**
     * @test
     */
    public function it_should_decode_indexed_array_into_an_array()
    {
        $data = ['foo' => 'bar'];

        $json = json_encode($data);

        $transformer = new JsonEncoder();

        $this->assertEquals($data, $transformer->reverseTransform($json));
    }

    /**
     * @test
     */
    public function decoding_null_should_return_null()
    {
        $transformer = new JsonEncoder();

        $this->assertNull($transformer->reverseTransform(null));
    }
}
