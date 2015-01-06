<?php

namespace Tests\BlackBox\Adapter;

use BlackBox\Adapter\MapAdapter;
use BlackBox\Backend\MemoryStorage;

/**
 * @covers \BlackBox\Adapter\MapAdapter
 */
class MapAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_store_data_as_map()
    {
        $storage = new MemoryStorage();
        $map = new MapAdapter($storage);

        $this->assertNull($map->get('foo'));

        $map->set('foo', 'bar');
        $map->set('test', 'Hello');
        $this->assertEquals('bar', $map->get('foo'));
        $this->assertEquals('Hello', $map->get('test'));
        $this->assertEquals(['foo' => 'bar', 'test' => 'Hello'], $storage->getData());

        $map->set('test', null);
        $this->assertEquals('bar', $map->get('foo'));
        $this->assertNull($map->get('test'));
        $this->assertEquals(['foo' => 'bar'], $storage->getData());
    }

    /**
     * @test
     */
    public function it_should_be_traversable()
    {
        $storage = new MemoryStorage();
        $map = new MapAdapter($storage);

        $this->assertEquals([], iterator_to_array($map));

        $map->set('foo', 'bar');

        $this->assertEquals([ 'foo' => 'bar' ], iterator_to_array($map));
    }
}
