<?php

namespace Tests\BlackBox\Transformer;

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

    public function setUp()
    {
        $this->transformer = new YamlEncoder();
    }

    /**
     * @test
     */
    public function it_should_encode_to_yaml()
    {
        $data = ['bar', 123];

        $transformed = $this->transformer->transform($data);

        $this->assertEquals(Yaml::dump($data), $transformed);
    }

    /**
     * @test
     */
    public function it_should_decode_from_yaml()
    {
        $data = ['bar', 123];

        $yaml = Yaml::dump($data);

        $this->assertEquals($data, $this->transformer->reverseTransform($yaml));
    }

    /**
     * @test
     */
    public function it_should_encode_primary_types()
    {
        $yaml = $this->transformer->transform('bar');

        $this->assertEquals('bar', $yaml);
    }

    /**
     * @test
     * @expectedException \BlackBox\Exception\StorageException
     * @expectedExceptionMessage The YamlEncoder cannot encode objects, stdClass given
     */
    public function it_should_not_encode_objects()
    {
        $this->transformer->transform(new \stdClass());
    }

    /**
     * @test
     */
    public function it_should_handle_get_null()
    {
        $this->assertNull($this->transformer->reverseTransform(null));
    }
}
