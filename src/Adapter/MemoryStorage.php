<?php

namespace BlackBox\Adapter;

use BlackBox\Storage;

/**
 * Stores the data in memory.
 *
 * This storage is not persistent! It is useful for mocking, tests or prototyping.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class MemoryStorage implements Storage
{
    /**
     * @var mixed
     */
    private $data;

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function setData($data)
    {
        $this->data = $data;
    }
}
