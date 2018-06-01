<?php

namespace BlackBox\Backend;

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
    private $storage = [];

    public function get($id)
    {
        return $this->storage[$id] ?? null;
    }

    public function set($id, $data)
    {
        $this->storage[$id] = $data;
    }

    public function remove($id)
    {
        unset($this->storage[$id]);
    }

    public function getIterator()
    {
        return yield from $this->storage;
    }
}
