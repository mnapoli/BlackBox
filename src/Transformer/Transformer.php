<?php

namespace BlackBox\Transformer;

use BlackBox\Storage;

/**
 * Storage decorator that transforms data going in and out.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
abstract class Transformer
{
    /**
     * @var Storage
     */
    private $storage;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    public function get($id)
    {
        $data = $this->storage->get($id);

        return $this->restore($data);
    }

    public function set($id, $data)
    {
        $data = $this->transform($data);

        $this->storage->set($id, $data);
    }

    public function remove($id)
    {
        $this->storage->remove($id);
    }

    abstract protected function transform($data);

    abstract protected function restore($data);
}
