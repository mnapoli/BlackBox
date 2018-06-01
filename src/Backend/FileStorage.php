<?php

namespace BlackBox\Backend;

use ArrayIterator;
use BlackBox\Exception\StorageException;
use BlackBox\Storage;
use BlackBox\Transformer\Transformer;
use IteratorAggregate;

/**
 * Stores data in a single file.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class FileStorage implements IteratorAggregate, Storage
{
    /**
     * @var string
     */
    private $filename;

    /**
     * @var Transformer
     */
    private $serializer;

    /**
     * @param string      $filename   File in which to store the data.
     * @param Transformer $serializer Transformer that serializes the data into string.
     */
    public function __construct($filename, Transformer $serializer)
    {
        $this->filename = (string) $filename;
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $data = $this->read();

        if (! array_key_exists($id, $data)) {
            return null;
        }

        return $data[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function set($id, $data)
    {
        $allData = $this->read();

        $allData[$id] = $data;

        $this->save($allData);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($id)
    {
        $allData = $this->read();

        unset($allData[$id]);

        $this->save($allData);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->read());
    }

    /**
     * @return array
     * @throws StorageException
     */
    private function read()
    {
        if (! file_exists($this->filename)) {
            return [];
        }

        if (! is_readable($this->filename)) {
            throw new StorageException(sprintf('The storage file "%s" is not readable', $this->filename));
        }

        $data = file_get_contents($this->filename);

        $data = $this->serializer->reverseTransform($data);

        if (! is_array($data)) {
            return [];
        }

        return $data;
    }

    /**
     * @param array $data
     */
    private function save(array $data)
    {
        $data = $this->serializer->transform($data);

        file_put_contents($this->filename, $data);
    }
}
