<?php

namespace BlackBox\Backend;

use BlackBox\Exception\StorageException;
use BlackBox\Storage;
use IteratorAggregate;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Stores data as multiple files in a directory.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class DirectoryStorage implements IteratorAggregate, Storage
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
     * @param string $directory Directory in which to store the data.
     * @param string $fileExtension File extension to use (if null, no extension is used).
     *
     * @throws StorageException The directory doesn't exist and cannot be created.
     */
    public function __construct(string $directory, string $fileExtension = null)
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

    public function get(string $id)
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

    public function set(string $id, $data) : void
    {
        $filename = $this->getFilename($id);

        file_put_contents($filename, $data);
    }

    public function remove(string $id) : void
    {
        $filename = $this->getFilename($id);

        if (file_exists($filename)) {
            unlink($filename);
        }
    }

    public function getIterator()
    {
        yield from $this->getAll();
    }

    /**
     * Builds a filename from a storage ID.
     */
    private function getFilename(string $id) : string
    {
        $extension = $this->fileExtension ? '.' . $this->fileExtension : '';

        return $this->directory . '/' . $this->encodeFilename($id) . $extension;
    }

    /**
     * @return array Get all entries as an array.
     */
    private function getAll() : array
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

    private function encodeFilename(string $filename) : string
    {
        $filename = urlencode($filename);
        $filename = str_replace('.', '%2E', $filename);
        $filename = str_replace('-', '%2D', $filename);

        return $filename;
    }
}
