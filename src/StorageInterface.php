<?php

namespace BlackBox;

use BlackBox\Exception\StorageException;

/**
 * Stores data.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
interface StorageInterface
{
    /**
     * Returns the data stored under the given ID.
     *
     * @param string $id
     *
     * @throws StorageException Error while retrieving the data.
     *
     * @return mixed
     */
    public function get($id);

    /**
     * Stores data under the given ID.
     *
     * @param string $id
     * @param mixed $data
     *
     * @throws StorageException Error while storing the data.
     *
     * @return void
     */
    public function set($id, $data);
}
