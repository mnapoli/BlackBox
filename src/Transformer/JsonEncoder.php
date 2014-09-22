<?php

namespace BlackBox\Transformer;

use BlackBox\StorageInterface;

/**
 * Encodes and decodes data into JSON.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class JsonEncoder implements StorageInterface
{
    /**
     * @var StorageInterface
     */
    private $wrapped;

    /**
     * @var bool
     */
    private $pretty;

    /**
     * @param StorageInterface $wrapped Wrapped storage.
     * @param bool             $pretty  Should the JSON be formatted for being read by a human?
     */
    public function __construct(StorageInterface $wrapped, $pretty = false)
    {
        $this->wrapped = $wrapped;
        $this->pretty = $pretty;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $data = $this->wrapped->get($id);

        return json_decode($data);
    }

    /**
     * {@inheritdoc}
     */
    public function set($id, $data)
    {
        $options = $this->pretty ? JSON_PRETTY_PRINT : 0;

        $data = json_encode($data, $options);

        $this->wrapped->set($id, $data);
    }
}
