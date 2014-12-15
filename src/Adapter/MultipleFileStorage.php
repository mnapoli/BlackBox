<?php

namespace BlackBox\Adapter;

use BlackBox\Exception\StorageException;
use BlackBox\MapStorage;

/**
 * Stores data in multiple files.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class MultipleFileStorage implements MapStorage
{
    /**
     * @var string
     */
    private $directory;

    /**
     * @var string
     */
    private $fileExtension;

    /**
     * @var FileStorage[]
     */
    private $files = [];

    /**
     * @param string $directory     Directory in which to set the data.
     * @param string $fileExtension File extension to use (if null, no extension is used).
     *
     * @throws StorageException The directory doesn't exist.
     */
    public function __construct($directory, $fileExtension = null)
    {
        $this->directory = (string) $directory;
        $this->fileExtension = ltrim($fileExtension, '.');

        if (! is_dir($this->directory)) {
            $success = mkdir($this->directory);
            if (! $success) {
                throw new StorageException(sprintf(
                    'The directory "%s" does not exist and cannot be created',
                    $this->directory
                ));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        return $this->getFileStorage($id)->getData();
    }

    /**
     * {@inheritdoc}
     */
    public function set($id, $data)
    {
        $this->getFileStorage($id)->setData($data);
    }

    /**
     * @param string $id
     *
     * @return FileStorage
     */
    private function getFileStorage($id)
    {
        if (! isset($this->files[$id])) {
            $this->files[$id] = new FileStorage($this->getFilename($id));
        }

        return $this->files[$id];
    }

    /**
     * Builds a filename from a storage ID.
     *
     * @param string $id
     *
     * @return string
     */
    private function getFilename($id)
    {
        $extension = $this->fileExtension ? '.' . $this->fileExtension : '';

        // TODO escape the ID
        return $this->directory . '/' . $id . $extension;
    }
}
