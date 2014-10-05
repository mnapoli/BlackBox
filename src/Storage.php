<?php

namespace BlackBox;

use BlackBox\Exception\StorageException;

/**
 * Stores data.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
interface Storage
{
    /**
     * Returns the data stored.
     *
     * @throws StorageException Error while retrieving the data.
     *
     * @return mixed Returns null if the storage was empty.
     */
    public function getData();

    /**
     * Stores data in the storage.
     *
     * If the storage contained anything previously, it will be overwritten.
     *
     * @param mixed $data
     *
     * @throws StorageException Error while storing the data.
     *
     * @return void
     */
    public function setData($data);
}
