<?php

namespace BlackBox\Backend;

use BlackBox\Exception\StorageException;
use BlackBox\Storage;

/**
 * Stores data in a single file.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class FileStorage implements Storage
{
    /**
     * @var string
     */
    private $filename;

    /**
     * @param string $filename File in which to store the data.
     */
    public function __construct($filename)
    {
        $this->filename = (string) $filename;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        if (! file_exists($this->filename)) {
            return null;
        }

        if (! is_readable($this->filename)) {
            throw new StorageException(sprintf('The storage file "%s" is not readable', $this->filename));
        }

        return file_get_contents($this->filename);
    }

    /**
     * {@inheritdoc}
     */
    public function setData($data)
    {
        file_put_contents($this->filename, $data);
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }
}
