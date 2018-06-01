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

    public function get(string $id)
    {
        return $this->storage[$id] ?? null;
    }

    public function set(string $id, $data) : void
    {
        $this->storage[$id] = $data;
    }

    public function remove(string $id) : void
    {
        unset($this->storage[$id]);
    }

    public function getIterator()
    {
        yield from $this->storage;
    }
}
