<?php

namespace Tests\BlackBox\Transformer;

use BlackBox\Adapter\ArrayStorage;
use BlackBox\Transformer\PhpSerializeEncoder;

/**
 * @covers \BlackBox\Transformer\PhpSerializeEncoder
 */
class PhpSerializeEncoderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_serialize()
    {
        $wrapped = new ArrayStorage();
        $storage = new PhpSerializeEncoder($wrapped);

        $data = ['bar', 123, new \stdClass()];

        $storage->set('foo', $data);

        $this->assertEquals(serialize($data), $wrapped->get('foo'));
    }

    /**
     * @test
     */
    public function it_should_unserialize()
    {
        $data = ['bar', 123, new \stdClass()];

        $wrapped = new ArrayStorage();
        $wrapped['foo'] = serialize($data);

        $storage = new PhpSerializeEncoder($wrapped);

        $this->assertEquals($data, $storage->get('foo'));
    }
}
