<?php

namespace Tests\BlackBox\Adapter;

use BlackBox\Backend\ArrayStorage;
use BlackBox\Transformer\JsonEncoder;
use BlackBox\Adapter\StorageWithTransformers;

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
        $storage = new StorageWithTransformers($backend);
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
        $storage = new StorageWithTransformers($backend);
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
        $storage = new StorageWithTransformers($backend);
        $storage->addTransformer(new JsonEncoder);

        $this->assertEquals([], iterator_to_array($storage));

        $storage->set('foo', 'bar');
        $this->assertEquals(['foo' => 'bar'], iterator_to_array($storage));
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

        $backend = new ArrayStorage;
        $storage = new StorageWithTransformers($backend);
        $storage->addTransformer($firstTransformer);
        $storage->addTransformer($secondTransformer);

        $storage->set('foo', 'bar');

        $this->assertEquals('bar - first - second', $backend->get('foo'));
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

        $backend = new ArrayStorage;
        $storage = new StorageWithTransformers($backend);
        $storage->addTransformer($firstTransformer);
        $storage->addTransformer($secondTransformer);

        $backend->set('foo', 'bar');

        $this->assertEquals('bar - second - first', $storage->get('foo'));
    }
}
