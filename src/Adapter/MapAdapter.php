<?php

namespace BlackBox\Adapter;

use ArrayIterator;
use BlackBox\MapStorage;
use BlackBox\Storage;
use IteratorAggregate;

/**
 * This adapter lets you use a Storage as a MapStorage.
 *
 * It offers the MapStorage API using an array stored in a single-value Storage.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class MapAdapter implements IteratorAggregate, MapStorage
{
    /**
     * @var Storage
     */
    private $storage;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $array = $this->storage->getData();

        if (! is_array($array)) {
            return null;
        }

        if (! array_key_exists($id, $array)) {
            return null;
        }

        return $array[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function set($id, $data)
    {
        $array = $this->storage->getData();

        if (! is_array($array)) {
            $array = [];
        }

        if ($data === null) {
            unset($array[$id]);
        } else {
            $array[$id] = $data;
        }

        $this->storage->setData($array);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        $array = $this->storage->getData();

        if (! is_array($array)) {
            $array = [];
        }

        return new ArrayIterator($array);
    }
}
