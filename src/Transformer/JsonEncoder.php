<?php

namespace BlackBox\Transformer;

/**
 * Encodes and decodes data into JSON.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class JsonEncoder implements Transformer
{
    /**
     * @var bool
     */
    private $pretty;

    /**
     * @param bool $pretty Should the JSON be formatted for being read by a human?
     */
    public function __construct($pretty = false)
    {
        $this->pretty = $pretty;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($data)
    {
        $options = $this->pretty ? JSON_PRETTY_PRINT : 0;

        return json_encode($data, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($data)
    {
        if ($data === null) {
            return null;
        }

        return json_decode($data, true);
    }
}
