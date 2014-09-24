<?php

namespace Tests\BlackBox\Transformer;

use BlackBox\Adapter\ArrayStorage;
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
        $wrapped = new ArrayStorage();
        $storage = new JsonEncoder($wrapped);

        $data = ['bar', 123];

        $storage->set('foo', $data);

        $this->assertEquals(json_encode($data), $wrapped->get('foo'));
    }

    /**
     * @test
     */
    public function it_should_encode_into_json_pretty()
    {
        $wrapped = new ArrayStorage();
        $storage = new JsonEncoder($wrapped, true);

        $data = ['bar', 123];

        $storage->set('foo', $data);

        $this->assertEquals(json_encode($data, JSON_PRETTY_PRINT), $wrapped->get('foo'));
    }

    /**
     * @test
     */
    public function it_should_decode_from_json()
    {
        $data = ['bar', 123];

        $wrapped = new ArrayStorage();
        $wrapped['foo'] = json_encode($data);

        $storage = new JsonEncoder($wrapped);

        $this->assertEquals($data, $storage->get('foo'));
    }

    /**
     * @test
     */
    public function it_should_handle_get_null()
    {
        $wrapped = new ArrayStorage();
        $wrapped['foo'] = null;

        $storage = new JsonEncoder($wrapped);

        $this->assertNull($storage->get('foo'));
    }
}
