<?php

namespace BlackBox\Adapter;

use BlackBox\Storage;
use BlackBox\Transformer\Transformer;

/**
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class StorageWithTransformers implements Storage
{
    /**
     * @var Storage
     */
    private $storage;

    /**
     * @var Transformer[]
     */
    private $transformers;

    /**
     * @param Storage       $storage
     * @param Transformer[] $transformers
     */
    public function __construct(Storage $storage, array $transformers = [])
    {
        $this->storage = $storage;
        $this->transformers = $transformers;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $data = $this->storage->getData();

        return $this->reverseTransform($data);
    }

    /**
     * {@inheritdoc}
     */
    public function setData($data)
    {
        $data = $this->transform($data);

        $this->storage->setData($data);
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
