<?php

namespace BlackBox\Cache;

use BlackBox\Storage;
use IteratorAggregate;

/**
 * Caches a storage with another storage.
 * Iterating this storage will not use the cache.
 *
 * @author Carlos Lombarte <lombartec@gmail.com>
 */
class StorageCache implements IteratorAggregate, Storage
{
    /**
     * The storage that will be cached.
     *
     * @var Storage
     */
    private $sourceStorage;

    /**
     * The cache used to storage the decorated storage.
     *
     * @var Storage
     */
    private $storageCache;

    /**
     * @param Storage $sourceStorage
     * @param Storage $storageCache
     */
    public function __construct(Storage $sourceStorage, Storage $storageCache)
    {
        $this->sourceStorage    = $sourceStorage;
        $this->storageCache     = $storageCache;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $cachedData = $this->storageCache->get($id);

        if (null !== $cachedData) {
            return $cachedData;
        }

        $sourceData = $this->sourceStorage->get($id);
        $this->storageCache->set($id, $sourceData);

        return $sourceData;
    }

    /**
     * {@inheritdoc}
     */
    public function set($id, $data)
    {
        $this->sourceStorage->set($id, $data);
        $this->storageCache->set($id, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function add($data)
    {
        $cachedDataId = $this->storageCache->add($data);
        $this->sourceStorage->set($cachedDataId, $data);

        return $cachedDataId;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($id)
    {
        $this->sourceStorage->remove($id);
        $this->storageCache->remove($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return $this->sourceStorage->getIterator();
    }
}
