<?php

namespace BlackBox\Adapter;

use BlackBox\Exception\StorageException;
use BlackBox\MapStorage;
use BlackBox\Storage;

/**
 * Stores data in memory in an array.
 *
 * This storage is not persistent!
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class ArrayStorage implements Storage, MapStorage, \ArrayAccess
{
    /**
     * @var array
     */
    private $storage = [];

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->storage;
    }

    /**
     * {@inheritdoc}
     */
    public function setData($data)
    {
        if (! is_array($data)) {
            throw new StorageException(sprintf('ArrayStorage can only store an array, %s given', gettype($data)));
        }

        $this->storage = $data;
    }

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

    public function offsetExists($id)
    {
        return array_key_exists($id, $this->storage);
    }

    public function offsetGet($id)
    {
        return $this->get($id);
    }

    public function offsetSet($id, $data)
    {
        $this->set($id, $data);
    }

    public function offsetUnset($id)
    {
        if (! array_key_exists($id, $this->storage)) {
            return;
        }

        unset($this->storage[$id]);
    }
}
