<?php

namespace BlackBox\Transformer;

/**
 * Transforms data.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
interface Transformer
{
    /**
     * @param mixed $data
     * @return mixed
     */
    public function transform($data);

    /**
     * @param mixed $data
     * @return mixed
     */
    public function reverseTransform($data);
}
