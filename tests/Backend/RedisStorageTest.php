<?php

namespace Tests\BlackBox\Backend;

use BlackBox\Backend\RedisStorage;
use PHPUnit_Framework_MockObject_MockObject;
use Predis\Client;

/**
 * @covers \BlackBox\Backend\RedisStorage
 */
class RedisStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RedisStorage
     */
    private $storage;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Client
     */
    private $redis;

    public function setUp()
    {
        parent::setUp();

        $this->redis = $this->getMock('Predis\Client', ['set', 'get', 'del', 'keys'], [], '', false);
        $this->storage = new RedisStorage($this->redis);
    }

    /**
     * @test
     */
    public function it_should_set_values()
    {
        $this->redis->expects($this->once())
            ->method('set')
            ->with('foo', 'bar');

        $this->storage->set('foo', 'bar');
    }

    /**
     * @test
     */
    public function set_null_should_delete()
    {
        $this->redis->expects($this->once())
            ->method('del')
            ->with(['foo']);

        $this->storage->set('foo', null);
    }

    /**
     * @test
     */
    public function it_should_get_values()
    {
        $this->redis->expects($this->once())
            ->method('get')
            ->with('foo')
            ->willReturn('bar');

        $this->assertEquals('bar', $this->storage->get('foo'));
    }

    /**
     * @test
     */
    public function get_non_existent_key_should_return_null()
    {
        $this->redis->expects($this->once())
            ->method('get')
            ->with('foo')
            ->willReturn(null);

        $this->assertSame(null, $this->storage->get('foo'));
    }
}
