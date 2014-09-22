<?php

namespace BlackBox\Transformer;

use BlackBox\StorageInterface;

/**
 * Encodes and decodes data using PHP's serialize functions.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class PhpSerializeEncoder implements StorageInterface
{
    /**
     * @var StorageInterface
     */
    private $wrapped;

    /**
     * @param StorageInterface $wrapped Wrapped storage.
     */
    public function __construct(StorageInterface $wrapped)
    {
        $this->wrapped = $wrapped;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $data = $this->wrapped->get($id);

        return unserialize($data);
    }

    /**
     * {@inheritdoc}
     */
    public function set($id, $data)
    {
        $data = serialize($data);

        $this->wrapped->set($id, $data);
    }
}
