<?php

namespace BlackBox\Adapter;

use ArrayIterator;
use BlackBox\MapStorage;
use IteratorAggregate;

/**
 * Stores data in memory in an array.
 *
 * This storage is not persistent!
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class ArrayStorage implements IteratorAggregate, MapStorage
{
    /**
     * @var array
     */
    private $storage = [];

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        if (! array_key_exists($id, $this->storage)) {
            return null;
        }

        return $this->storage[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function set($id, $data)
    {
        $this->storage[$id] = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->storage);
    }
}
