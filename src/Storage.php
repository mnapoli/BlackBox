<?php

namespace BlackBox;

use BlackBox\Exception\StorageException;
use Traversable;

/**
 * Storage.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 * @author Christopher Pitt <cgpitt@gmail.com>
 */
interface Storage extends Traversable
{
    /**
     * Returns the data stored under the given ID.
     *
     * @throws StorageException Error while retrieving the data.
     *
     * @return mixed|null Null should be returned when no data is found.
     */
    public function get(string $id);

    /**
     * Stores data under the given ID.
     *
     * @param mixed $data
     *
     * @throws StorageException Error while storing the data.
     */
    public function set(string $id, $data) : void;

    /**
     * Removes data stored under the given ID.
     *
     * @throws StorageException Error while removing the data.
     */
    public function remove(string $id) : void;
}
