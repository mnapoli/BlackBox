<?php

namespace BlackBox\Adapter;

use BlackBox\Exception\StorageException;
use BlackBox\StorageInterface;

/**
 * Stores data in multiple files.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class MultipleFileStorage implements StorageInterface
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
     * @param string $directory     Directory in which to set the data.
     * @param string $fileExtension File extension to use (if null, no extension is used).
     *
     * @throws StorageException The directory doesn't exist.
     */
    public function __construct($directory, $fileExtension = null)
    {
        $this->directory = (string) $directory;
        $this->fileExtension = (string) $fileExtension;

        if (! is_dir($this->directory)) {
            throw new StorageException(sprintf('The directory "%s" does not exist', $this->directory));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $filename = $this->getFilename($id);

        if (! file_exists($filename)) {
            return null;
        }

        return file_get_contents($filename);
    }

    /**
     * {@inheritdoc}
     */
    public function set($id, $data)
    {
        $filename = $this->getFilename($id);

        file_put_contents($filename, $data);
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
