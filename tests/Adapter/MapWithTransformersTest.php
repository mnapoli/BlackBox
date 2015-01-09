<?php

namespace Tests\BlackBox\Adapter;

use BlackBox\Backend\ArrayStorage;
use BlackBox\Transformer\JsonEncoder;
use BlackBox\Adapter\MapWithTransformers;

/**
 * @covers \BlackBox\Adapter\MapWithTransformers
 */
class MapWithTransformersTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function set_should_use_transformers()
    {
        $backend = new ArrayStorage;
        $storage = new \BlackBox\Adapter\MapWithTransformers($backend);
        $storage->addTransformer(new JsonEncoder);

        $storage->set('foo', 'bar');

        $this->assertEquals('"bar"', $backend->get('foo'));
    }

    /**
     * @test
     */
    public function get_should_use_transformers()
    {
        $backend = new ArrayStorage;
        $storage = new MapWithTransformers($backend);
        $storage->addTransformer(new JsonEncoder);

        $this->assertNull($storage->get('foo'));

        $storage->set('foo', 'bar');
        $this->assertEquals('bar', $storage->get('foo'));
    }

    /**
     * @test
     */
    public function traversable_should_use_transformers()
    {
        $backend = new ArrayStorage;
        $storage = new \BlackBox\Adapter\MapWithTransformers($backend);
        $storage->addTransformer(new JsonEncoder);

        $this->assertEquals([], iterator_to_array($storage));

        $storage->set('foo', 'bar');
        $this->assertEquals(['foo' => 'bar'], iterator_to_array($storage));
    }
}
