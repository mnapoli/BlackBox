<?php

namespace BlackBox\Backend;

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
     * @param string $filename File in which to store the data.
     * @param Transformer $serializer Transformer that serializes the data into string.
     */
    public function __construct(string $filename, Transformer $serializer)
    {
        $this->filename = $filename;
        $this->serializer = $serializer;
    }

    public function get(string $id)
    {
        $data = $this->read();

        return $data[$id] ?? null;
    }

    public function set(string $id, $data) : void
    {
        $allData = $this->read();

        $allData[$id] = $data;

        $this->save($allData);
    }

    public function remove(string $id) : void
    {
        $allData = $this->read();

        unset($allData[$id]);

        $this->save($allData);
    }

    public function getIterator()
    {
        yield from $this->read();
    }

    /**
     * @throws StorageException
     */
    private function read() : array
    {
        if (! file_exists($this->filename)) {
            return [];
        }

        if (! is_readable($this->filename)) {
            throw new StorageException(sprintf('The storage file "%s" is not readable', $this->filename));
        }

        $data = file_get_contents($this->filename);

        $data = $this->serializer->restore($data);

        if (! is_array($data)) {
            return [];
        }

        return $data;
    }

    private function save(array $data)
    {
        $data = $this->serializer->transform($data);

        file_put_contents($this->filename, $data);
    }
}
