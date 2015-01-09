<?php

namespace Tests\BlackBox\FunctionalTest;

use BlackBox\Backend\RedisStorage;
use Predis\Client;
use Predis\Connection\ConnectionException;

/**
 * Tests with a live instance of Redis.
 */
class RedisTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RedisStorage
     */
    private $storage;

    public function setUp()
    {
        parent::setUp();

        try {
            $redis = new Client();
            $redis->ping();
        } catch (ConnectionException $e) {
            $this->markTestSkipped('Impossible to connect to Redis at tcp://127.0.0.1:6379');
        }
        $this->storage = RedisStorage::create();
    }

    /**
     * @test
     */
    public function it_should_store_data()
    {
        $this->storage->set('foo', 'bar');
        $this->assertEquals('bar', $this->storage->get('foo'));
    }

    /**
     * @test
     */
    public function get_non_existent_key_should_return_null()
    {
        $this->assertSame(null, $this->storage->get('foo'));
    }

    /**
     * @test
     */
    public function set_null_should_delete()
    {
        $this->storage->set('foo', 'bar');
        $this->storage->set('foo', null);
        $this->assertSame(null, $this->storage->get('foo'));
    }
}
