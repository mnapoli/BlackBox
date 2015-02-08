<?php

namespace Tests\BlackBox\FunctionalTest;

use BlackBox\Backend\RedisStorage;
use Predis\Client;
use Predis\Connection\ConnectionException;
use Tests\BlackBox\BaseStorageTest;

/**
 * Tests with a live instance of Redis.
 */
class RedisTest extends BaseStorageTest
{
    /**
     * @var RedisStorage
     */
    protected $storage;

    public function setUp()
    {
        parent::setUp();

        try {
            $redis = new Client();
            $redis->ping();
        } catch (ConnectionException $e) {
            $this->markTestSkipped('Impossible to connect to Redis at tcp://127.0.0.1:6379');
            return;
        }

        // Clear the current db
        $redis->flushdb();

        $this->storage = new RedisStorage($redis);
    }
}
