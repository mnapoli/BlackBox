<?php

namespace Tests\BlackBox\Backend;

use BlackBox\Backend\CacheListStorage;
use Mockery;
use Mockery\MockInterface;

/**
 * @covers CacheListStorage
 */
class CacheListStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MockInterface
     */
    protected $cache;

    /**
     * @var CacheListStorage
     */
    protected $storage;

    protected function setUp()
    {
        parent::setUp();

        $this->cache = Mockery::mock('Doctrine\Common\Cache\Cache');

        $this->storage = new CacheListStorage($this->cache);
    }

    protected function tearDown()
    {
        parent::tearDown();

        Mockery::close();
    }

    /**
     * @test
     */
    public function it_should_be_traversable()
    {
        $this->cache
            ->shouldReceive('fetch')
            ->with('keys')
            ->andReturn([]);

        $this->assertEquals([], iterator_to_array($this->storage));
    }

    /**
     * @test
     */
    public function it_should_add_values()
    {
        $this->cache
            ->shouldReceive('fetch')
            ->with('keys');

        $this->cache
            ->shouldReceive('save')
            ->with('keys', ['item.1']);

        $this->cache
            ->shouldReceive('save')
            ->with('item.1', 'foo');

        $this->storage->add('foo');
    }

    /**
     * @test
     */
    public function it_should_fetch_values()
    {
        $this->cache
            ->shouldReceive('fetch')
            ->with('keys')
            ->andReturn(['item.1']);

        $this->cache
            ->shouldReceive('fetch')
            ->with('item.1');

        iterator_to_array($this->storage);
    }

    /**
     * @test
     */
    public function it_should_remove_values()
    {
        $this->cache
            ->shouldReceive('fetch')
            ->with('keys')
            ->andReturn(['item.1', 'item.2']);

        $this->cache
            ->shouldReceive('fetch')
            ->with('item.1')
            ->andReturn('foo');

        $this->cache
            ->shouldReceive('fetch')
            ->with('item.2')
            ->andReturn('bar');

        $this->cache
            ->shouldReceive('delete')
            ->with('item.1');

        $this->cache
            ->shouldReceive('save')
            ->with('keys', ['item.2']);

        $this->storage->remove('foo');
    }
}
