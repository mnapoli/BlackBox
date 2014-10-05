<?php

namespace BlackBox\Transformer;

use BlackBox\MapStorage;

/**
 * Encodes and decodes data using PHP's serialize functions.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class PhpSerializeEncoder extends AbstractTransformer implements MapStorage
{
    /**
     * {@inheritdoc}
     */
    protected function transform($data)
    {
        return serialize($data);
    }

    /**
     * {@inheritdoc}
     */
    protected function reverseTransform($data)
    {
        return unserialize($data);
    }
}
