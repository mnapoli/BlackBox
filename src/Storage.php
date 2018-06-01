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
     * @param string|int $id
     *
     * @throws StorageException Error while retrieving the data.
     *
     * @return mixed|null Null should be returned when no data is found.
     */
    public function get($id);

    /**
     * Stores data under the given ID.
     *
     * @param string|int $id
     * @param mixed      $data
     *
     * @throws StorageException Error while storing the data.
     *
     * @return void
     */
    public function set($id, $data);

    /**
     * Removes data stored under the given ID.
     *
     * @param string|int $id
     *
     * @throws StorageException Error while removing the data.
     *
     * @return void
     */
    public function remove($id);
}
