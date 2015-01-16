<?php

namespace BlackBox\Backend;

use ArrayIterator;
use BlackBox\Exception\StorageException;
use BlackBox\MapStorage;
use IteratorAggregate;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Stores data in multiple files.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class MultipleFileStorage implements IteratorAggregate, MapStorage
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
        $filename = $this->getFilename($id);

        if (! file_exists($filename)) {
            return null;
        }

        if (! is_readable($filename)) {
            throw new StorageException(sprintf('The storage file "%s" is not readable', $filename));
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
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->getAll());
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

        return $this->directory . '/' . $this->encodeFilename($id) . $extension;
    }

    /**
     * @return array Get all entries as an array.
     */
    private function getAll()
    {
        $files = new Finder();
        $files->files()->in($this->directory);
        if ($this->fileExtension) {
            $files->name('*.' . $this->fileExtension);
        }

        $data = [];
        foreach ($files as $file) {
            /** @var SplFileInfo $file */
            $id = $file->getFilename();
            if ($this->fileExtension) {
                $id = substr($id, 0, -strlen('.' . $this->fileExtension));
            }

            $data[$id] = $this->get($id);
        }

        return $data;
    }

    private function encodeFilename($filename)
    {
        $filename = urlencode($filename);
        $filename = str_replace('.', '%2E', $filename);
        $filename = str_replace('-', '%2D', $filename);

        return $filename;
    }
}
