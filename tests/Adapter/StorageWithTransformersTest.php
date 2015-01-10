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

    /**
     * @test
     */
    public function set_should_call_transformers_in_order()
    {
        $firstTransformer = $this->getMockForAbstractClass('BlackBox\Transformer\Transformer');
        $firstTransformer->expects($this->once())
            ->method('transform')
            ->willReturnCallback(function ($data) {
                return $data . ' - first';
            });

        $secondTransformer = $this->getMockForAbstractClass('BlackBox\Transformer\Transformer');
        $secondTransformer->expects($this->once())
            ->method('transform')
            ->willReturnCallback(function ($data) {
                return $data . ' - second';
            });

        $backend = new MemoryStorage;
        $storage = new StorageWithTransformers($backend);
        $storage->addTransformer($firstTransformer);
        $storage->addTransformer($secondTransformer);

        $storage->setData('foo');

        $this->assertEquals('foo - first - second', $backend->getData());
    }

    /**
     * @test
     */
    public function get_should_call_transformers_in_revers_order()
    {
        $firstTransformer = $this->getMockForAbstractClass('BlackBox\Transformer\Transformer');
        $firstTransformer->expects($this->once())
            ->method('reverseTransform')
            ->willReturnCallback(function ($data) {
                return $data . ' - first';
            });

        $secondTransformer = $this->getMockForAbstractClass('BlackBox\Transformer\Transformer');
        $secondTransformer->expects($this->once())
            ->method('reverseTransform')
            ->willReturnCallback(function ($data) {
                return $data . ' - second';
            });

        $backend = new MemoryStorage;
        $storage = new StorageWithTransformers($backend);
        $storage->addTransformer($firstTransformer);
        $storage->addTransformer($secondTransformer);

        $backend->setData('foo');

        $this->assertEquals('foo - second - first', $storage->getData());
    }
}
