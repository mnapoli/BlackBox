<?php

namespace BlackBox\Backend;

use ArrayIterator;
use BlackBox\ListStorage;
use Doctrine\Common\Cache\Cache;
use IteratorAggregate;

/**
 * Stores data in cache.
 *
 * @author Christopher Pitt <cgpitt@gmail.com>
 */
class CacheListStorage implements IteratorAggregate, ListStorage
{
    /**
     * @var int
     */
    protected $id = 0;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var array
     */
    protected $storage = [];

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new ArrayIterator(
            array_values($this->fetchAll())
        );
    }

    /**
     * Fetches all items.
     *
     * @return array
     */
    protected function fetchAll()
    {
        $items = [];

        if ($keys = $this->cache->fetch("keys")) {
            $items = $this->fetchAllWith($keys);
        }

        return $items;
    }

    /**
     * Fetches all items matching provided keys.
     *
     * @param array $keys
     *
     * @return array
     */
    protected function fetchAllWith(array $keys)
    {
        $items = [];

        foreach ($keys as $key) {
            $items[$key] = $this->cache->fetch($key);
        }

        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function add($data)
    {
        $key = $this->getNewKey();

        $this->addKey($key);
        $this->saveData($key, $data);
    }

    /**
     * Generates a new cache key for this data.
     *
     * @return string
     */
    protected function getNewKey()
    {
        $this->id++;

        return "item.{$this->id}";
    }

    /**
     * Adds a new key to the list of keys in cache.
     *
     * @param string $key
     */
    protected function addKey($key)
    {
        $keys = $this->cache->fetch("keys") ?: [];

        $keys[] = $key;

        $this->cache->save("keys", $keys);
    }

    /**
     * Saves data to the cache.
     *
     * @param string $key
     * @param string $data
     *
     * @return string
     */
    protected function saveData($key, $data)
    {
        $this->cache->save($key, $data);

        return $key;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($data)
    {
        $keys = [];

        foreach ($this->fetchAll() as $key => $item) {
            if ($item === $data) {
                $this->cache->delete($key);
            } else {
                $keys[] = $key;
            }
        }

        $this->cache->save("keys", $keys);

        return $this;
    }
}
