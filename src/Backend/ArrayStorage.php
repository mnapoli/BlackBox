<?php

namespace BlackBox\Backend;

use ArrayIterator;
use BlackBox\Storage;
use IteratorAggregate;

/**
 * Stores data in memory in an array.
 *
 * This storage is not persistent!
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class ArrayStorage implements IteratorAggregate, Storage
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
    public function add($data)
    {
        $this->storage[] = $data;

        end($this->storage);
        return key($this->storage);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($id)
    {
        unset($this->storage[$id]);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->storage);
    }
}
