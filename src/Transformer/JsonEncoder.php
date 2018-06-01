<?php

namespace BlackBox\Transformer;

use BlackBox\Storage;

/**
 * Encodes and decodes data into JSON.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class JsonEncoder extends Transformer
{
    /**
     * @var bool
     */
    private $pretty;

    /**
     * @param bool $pretty Should the JSON be formatted for being read by a human?
     */
    public function __construct(Storage $storage, $pretty = false)
    {
        parent::__construct($storage);

        $this->pretty = $pretty;
    }

    /**
     * {@inheritdoc}
     */
    protected function transform($data)
    {
        $options = $this->pretty ? JSON_PRETTY_PRINT : 0;

        return json_encode($data, $options);
    }

    /**
     * {@inheritdoc}
     */
    protected function restore($data)
    {
        if ($data === null) {
            return null;
        }

        return json_decode($data, true);
    }
}
