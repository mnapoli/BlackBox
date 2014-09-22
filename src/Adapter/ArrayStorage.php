<?php

namespace BlackBox\Adapter;

use BlackBox\StorageInterface;

/**
 * Stores data in memory in an array.
 *
 * This storage is not persistent!
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class ArrayStorage implements StorageInterface
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
        return $this->storage[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function set($id, $data)
    {
        $this->storage[$id] = $data;
    }
}
