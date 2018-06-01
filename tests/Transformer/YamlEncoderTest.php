<?php

namespace Tests\BlackBox\Transformer;

use BlackBox\Backend\ArrayStorage;
use BlackBox\Transformer\YamlEncoder;
use Symfony\Component\Yaml\Yaml;

/**
 * @covers \BlackBox\Transformer\YamlEncoder
 */
class YamlEncoderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var YamlEncoder
     */
    private $transformer;

    /**
     * @var ArrayStorage
     */
    private $storage;

    public function setUp()
    {
        $this->storage = new ArrayStorage;
        $this->transformer = new YamlEncoder($this->storage);
    }

    /**
     * @test
     */
    public function it_should_encode_to_yaml()
    {
        $this->transformer->set('foo', ['bar', 123]);

        $this->assertEquals(Yaml::dump(['bar', 123]), $this->storage->get('foo'));
    }

    /**
     * @test
     */
    public function it_should_decode_from_yaml()
    {
        $this->transformer->set('foo', ['bar', 123]);

        $this->assertEquals(['bar', 123], $this->transformer->get('foo'));
    }

    /**
     * @test
     */
    public function it_should_encode_primary_types()
    {
        $this->transformer->set('foo', 'bar');

        $this->assertEquals('bar', $this->storage->get('foo'));
    }

    /**
     * @test
     * @expectedException \BlackBox\Exception\StorageException
     * @expectedExceptionMessage The YamlEncoder cannot encode objects, stdClass given
     */
    public function it_should_not_encode_objects()
    {
        $this->transformer->set('foo', new \stdClass);
    }

    /**
     * @test
     */
    public function it_should_handle_get_null()
    {
        $this->transformer->set('foo', null);

        $this->assertNull($this->storage->get('foo'));
    }
}
