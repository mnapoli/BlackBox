<?php

namespace BlackBox\Transformer;

use BlackBox\Storage;
use BlackBox\MapStorage;

/**
 * Encodes and decodes data into JSON.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class JsonEncoder extends AbstractTransformer implements MapStorage
{
    /**
     * @var bool
     */
    private $pretty;

    /**
     * {@inheritdoc}
     * @param bool $pretty Should the JSON be formatted for being read by a human?
     */
    public function __construct(Storage $wrapped, $pretty = false)
    {
        parent::__construct($wrapped);
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
    protected function reverseTransform($data)
    {
        return json_decode($data, true);
    }
}
