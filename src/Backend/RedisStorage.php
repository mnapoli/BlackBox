<?php

namespace BlackBox\Backend;

use ArrayIterator;
use BlackBox\Id\IdGenerator;
use BlackBox\Id\RandomStringIdGenerator;
use BlackBox\Storage;
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
class RedisStorage implements IteratorAggregate, Storage
{
    /**
     * @var Client
     */
    private $redis;

    /**
     * @var IdGenerator
     */
    private $idGenerator;

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

    public function __construct(Client $redis, IdGenerator $idGenerator = null)
    {
        $this->redis = $redis;
        $this->idGenerator = $idGenerator ?: new RandomStringIdGenerator();
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
        $this->redis->set($id, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function add($data)
    {
        $id = $this->idGenerator->getId();

        $this->set($id, $data);

        return $id;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($id)
    {
        $this->redis->del([$id]);
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
