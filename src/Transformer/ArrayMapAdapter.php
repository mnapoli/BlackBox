<?php

namespace BlackBox\Transformer;

use BlackBox\MapStorage;
use BlackBox\Storage;

/**
 * This adapter lets you use a Storage as a MapStorage.
 *
 * It offers the MapStorage API using an array stored in a classic Storage.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class ArrayMapAdapter implements MapStorage
{
    /**
     * @var Storage
     */
    protected $wrapped;

    /**
     * @param Storage $wrapped Wrapped storage.
     */
    public function __construct(Storage $wrapped)
    {
        $this->wrapped = $wrapped;
    }

    /**
     * {@inheritdoc}
     * @return array
     */
    public function getData()
    {
        return $this->wrapped->getData();
    }

    /**
     * {@inheritdoc}
     * @param array $data
     */
    public function setData($data)
    {
        if (! is_array($data)) {
            throw new \InvalidArgumentException(sprintf(
                "ArrayMapAdapter::setData() only accepts arrays, %s given",
                is_object ($data) ? get_class($data) : gettype($data)
            ));
        }

        $this->wrapped->setData($data);
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $array = $this->wrapped->getData();

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
        $array = $this->wrapped->getData();

        if (! is_array($array)) {
            $array = [];
        }

        $array[$id] = $data;

        $this->wrapped->setData($array);
    }
}
