<?php

namespace BlackBox\Adapter;

use ArrayIterator;
use BlackBox\MapStorage;
use BlackBox\Transformer\Transformer;
use IteratorAggregate;

/**
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class MapWithTransformers implements IteratorAggregate, MapStorage
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

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        // TODO optimize
        $array = iterator_to_array($this->storage);

        $array = array_map(function ($item) {
            return $this->reverseTransform($item);
        }, $array);

        return new ArrayIterator($array);
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
