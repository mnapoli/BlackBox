<?php

namespace Tests\BlackBox\Transformer;

use BlackBox\Adapter\ArrayStorage;
use BlackBox\Transformer\YamlEncoder;
use Symfony\Component\Yaml\Yaml;

/**
 * @covers \BlackBox\Transformer\YamlEncoder
 */
class YamlEncoderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_encode_to_yaml()
    {
        $wrapped = new ArrayStorage();
        $storage = new YamlEncoder($wrapped);

        $data = ['bar', 123];

        $storage->set('foo', $data);

        $this->assertEquals(Yaml::dump($data), $wrapped->get('foo'));
    }

    /**
     * @test
     */
    public function it_should_decode_from_yaml()
    {
        $data = ['bar', 123];

        $wrapped = new ArrayStorage();
        $wrapped['foo'] = Yaml::dump($data);

        $storage = new YamlEncoder($wrapped);

        $this->assertEquals($data, $storage->get('foo'));
    }

    /**
     * @test
     * @expectedException \BlackBox\Exception\StorageException
     * @expectedExceptionMessage The YamlEncoder can only encode arrays, stdClass given
     */
    public function it_should_not_encode_objects()
    {
        $wrapped = new ArrayStorage();
        $storage = new YamlEncoder($wrapped);

        $storage->set('foo', new \stdClass());
    }

    /**
     * @test
     * @expectedException \BlackBox\Exception\StorageException
     * @expectedExceptionMessage The YamlEncoder can only encode arrays, string given
     */
    public function it_should_not_encode_non_arrays()
    {
        $wrapped = new ArrayStorage();
        $storage = new YamlEncoder($wrapped);

        $storage->set('foo', 'bar');
    }

    /**
     * @test
     */
    public function it_should_handle_get_null()
    {
        $wrapped = new ArrayStorage();
        $wrapped['foo'] = null;

        $storage = new YamlEncoder($wrapped);

        $this->assertNull($storage->get('foo'));
    }
}
