<?php

namespace BlackBox\Backend;

use ArrayIterator;
use BlackBox\MapStorage;
use IteratorAggregate;
use Predis\Client;

/**
 * Stores data in Redis.
 *
 * This storage requires the Predis library.
 *
 * @link https://github.com/nrk/predis
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class RedisStorage implements IteratorAggregate, MapStorage
{
    /**
     * @var Client
     */
    private $redis;

    /**
     * Creates a new instance by providing directly the Predis constructor arguments.
     *
     * @param mixed $parameters Connection parameters for one or more servers.
     * @param mixed $options    Options to configure some behaviours of the client.
     *
     * @return RedisStorage
     */
    public static function create($parameters = null, $options = null)
    {
        return new static(new Client($parameters, $options));
    }

    public function __construct(Client $redis)
    {
        $this->redis = $redis;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        return $this->redis->get($id);
    }

    /**
     * {@inheritdoc}
     */
    public function set($id, $data)
    {
        if ($data === null) {
            $this->redis->del([$id]);
            return;
        }

        $this->redis->set($id, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        // TODO optimize the shit out of this crap

        $keys = $this->redis->keys('*');

        $values = array_map(function ($key) {
            return $this->redis->get($key);
        }, $keys);

        $array = array_combine($keys, $values);

        return new ArrayIterator($array);
    }
}
