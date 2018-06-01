<?php

namespace BlackBox\Transformer;

use BlackBox\Storage;

/**
 * Storage decorator that transforms data going in and out.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
abstract class Transformer implements Storage
{
    /**
     * @var Storage
     */
    private $storage;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    public function get(string $id)
    {
        $data = $this->storage->get($id);

        return $this->restore($data);
    }

    public function set(string $id, $data) : void
    {
        $data = $this->transform($data);

        $this->storage->set($id, $data);
    }

    public function remove(string $id) : void
    {
        $this->storage->remove($id);
    }

    abstract protected function transform($data);

    abstract protected function restore($data);
}
