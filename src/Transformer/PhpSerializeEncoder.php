<?php

namespace BlackBox\Transformer;

/**
 * Encodes and decodes data using PHP's serialize functions.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class PhpSerializeEncoder implements Transformer
{
    /**
     * {@inheritdoc}
     */
    public function transform($data)
    {
        return serialize($data);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($data)
    {
        if ($data === null) {
            return null;
        }

        return unserialize($data);
    }
}
