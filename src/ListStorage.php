<?php

namespace BlackBox;

use BlackBox\Exception\StorageException;
use Traversable;

/**
 * Stores multiple items, in any order.
 *
 * @author Christopher Pitt <cgpitt@gmail.com>
 */
interface ListStorage extends Traversable
{
    /**
     * Adds data to the store.
     *
     * @param mixed $data
     *
     * @throws StorageException Error while storing the data.
     *
     * @return void
     */
    public function add($data);

    /**
     * Removes data from the store.
     *
     * @param mixed $data
     *
     * @throws StorageException Error while removing the data.
     *
     * @return void
     */
    public function remove($data);
}
