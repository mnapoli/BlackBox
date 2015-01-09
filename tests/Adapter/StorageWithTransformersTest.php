<?php

namespace Tests\BlackBox\Adapter;

use BlackBox\Adapter\StorageWithTransformers;
use BlackBox\Backend\MemoryStorage;
use BlackBox\Transformer\JsonEncoder;

/**
 * @covers \BlackBox\Adapter\StorageWithTransformers
 */
class StorageWithTransformersTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function set_should_use_transformers()
    {
        $backend = new MemoryStorage;
        $storage = new StorageWithTransformers($backend);
        $storage->addTransformer(new JsonEncoder);

        $storage->setData('foo');

        $this->assertEquals('"foo"', $backend->getData());
    }

    /**
     * @test
     */
    public function get_should_use_transformers()
    {
        $backend = new MemoryStorage;
        $storage = new StorageWithTransformers($backend);
        $storage->addTransformer(new JsonEncoder);

        $this->assertNull($storage->getData());

        $storage->setData('foo');
        $this->assertEquals('foo', $storage->getData());
    }
}
