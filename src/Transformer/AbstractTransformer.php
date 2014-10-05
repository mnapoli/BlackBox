<?php

namespace BlackBox\Transformer;

use BlackBox\Storage;
use BlackBox\MapStorage;

/**
 * AbstractTransformer
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
abstract class AbstractTransformer implements Storage, MapStorage
{
    /**
     * @var Storage
     */
    protected $wrapped;

    /**
     * @param Storage $wrapped Wrapped storage.
     */
    public function __construct(Storage $wrapped)
    {
        $this->wrapped = $wrapped;
    }

    /**
     * @param mixed $data
     * @return mixed
     */
    abstract protected function transform($data);

    /**
     * @param mixed $data
     * @return mixed
     */
    abstract protected function reverseTransform($data);

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $data = $this->wrapped->getData();

        if ($data === null) {
            return null;
        }

        return $this->reverseTransform($data);
    }

    /**
     * {@inheritdoc}
     */
    public function setData($data)
    {
        $this->wrapped->setData($this->transform($data));
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        if (! $this->wrapped instanceof MapStorage) {
            throw new \RuntimeException(sprintf(
                "The storage %s doesn't implement the MapStorage interface",
                get_class($this->wrapped)
            ));
        }

        $data = $this->wrapped->get($id);

        if ($data === null) {
            return null;
        }

        return $this->reverseTransform($data);
    }

    /**
     * {@inheritdoc}
     */
    public function set($id, $data)
    {
        if (! $this->wrapped instanceof MapStorage) {
            throw new \RuntimeException(sprintf(
                "The storage %s doesn't implement the MapStorage interface",
                get_class($this->wrapped)
            ));
        }

        $this->wrapped->set($id, $this->transform($data));
    }
}
