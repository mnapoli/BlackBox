<?php

namespace BlackBox\Transformer;

use BlackBox\MapStorage;

/**
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class MapWithTransformers implements MapStorage
{
    /**
     * @var MapStorage
     */
    private $storage;

    /**
     * @var Transformer[]
     */
    private $transformers;

    /**
     * @param MapStorage    $storage
     * @param Transformer[] $transformers
     */
    public function __construct(MapStorage $storage, array $transformers = [])
    {
        $this->storage = $storage;
        $this->transformers = $transformers;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $data = $this->storage->get($id);

        return $this->reverseTransform($data);
    }

    /**
     * {@inheritdoc}
     */
    public function set($id, $data)
    {
        $data = $this->transform($data);

        $this->storage->set($id, $data);
    }

    public function addTransformer(Transformer $transformer)
    {
        $this->transformers[] = $transformer;
    }

    /**
     * @param mixed $data
     * @return mixed
     */
    private function transform($data)
    {
        foreach ($this->transformers as $transformer) {
            $data = $transformer->transform($data);
        }

        return $data;
    }

    /**
     * @param mixed $data
     * @return mixed
     */
    private function reverseTransform($data)
    {
        foreach ($this->transformers as $transformer) {
            $data = $transformer->reverseTransform($data);
        }

        return $data;
    }
}
