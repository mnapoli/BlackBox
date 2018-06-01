<?php

namespace Tests\BlackBox\Transformer;

use BlackBox\Backend\ArrayStorage;
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
        $storage = new ArrayStorage;
        $transformer = new JsonEncoder($storage);

        $data = ['bar', 123];

        $transformer->set('foo', $data);

        $this->assertEquals(json_encode($data), $storage->get('foo'));
    }

    /**
     * @test
     */
    public function it_should_encode_into_json_pretty()
    {
        $storage = new ArrayStorage;
        $transformer = new JsonEncoder($storage, true);

        $data = ['bar', 123];

        $transformer->set('foo', $data);

        $this->assertEquals(json_encode($data, JSON_PRETTY_PRINT), $storage->get('foo'));
    }

    /**
     * @test
     */
    public function it_should_decode_from_json()
    {
        $transformer = new JsonEncoder(new ArrayStorage);

        $transformer->set('foo', ['bar', 123]);

        $this->assertEquals(['bar', 123], $transformer->get('foo'));
    }

    /**
     * @test
     */
    public function it_should_support_indexed_arrays()
    {
        $transformer = new JsonEncoder(new ArrayStorage);

        $transformer->set('foo', ['foo' => 'bar']);

        $this->assertEquals(['foo' => 'bar'], $transformer->get('foo'));
    }

    /**
     * @test
     */
    public function it_should_support_null()
    {
        $transformer = new JsonEncoder(new ArrayStorage);

        $transformer->set('foo', null);

        $this->assertNull($transformer->get('foo'));
    }
}
