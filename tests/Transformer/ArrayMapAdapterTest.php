<?php

namespace Tests\BlackBox\Transformer;

use BlackBox\Adapter\MemoryStorage;
use BlackBox\Transformer\ArrayMapAdapter;

/**
 * @covers \BlackBox\Transformer\ArrayMapAdapter
 */
class ArrayMapAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_store_as_array()
    {
        $wrapped = new MemoryStorage();
        $storage = new ArrayMapAdapter($wrapped);

        $storage->set('foo', 'bar');

        $this->assertEquals(['foo' => 'bar'], $wrapped->getData());
        $this->assertEquals(['foo' => 'bar'], $storage->getData());
        $this->assertEquals('bar', $storage->get('foo'));
    }

    /**
     * @test
     */
    public function it_should_return_null_for_non_existing_entries()
    {
        $storage = new ArrayMapAdapter(new MemoryStorage());

        $this->assertNull($storage->get('foo'));
    }
}
