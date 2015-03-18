<?php

namespace Tests\BlackBox;

use BlackBox\StorageCache;

/**
 * @covers \BlackBox\StorageCache
 *
 * @author Carlos Lombarte <lombartec@gmail.com>
 */
class StorageCacheTest extends \PHPUnit_Framework_TestCase
{
    /**
    * Subject under test.
    *
    * @var StorageCache
    */
    protected $storage;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $sourceStorageMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $storageCacheMock;

    public function setUp()
    {
        $this->sourceStorageMock    = $this->getMock(
            'BlackBox\Storage',
            ['get', 'set', 'add', 'remove', 'getIterator']
        );
        $this->storageCacheMock     = $this->getMock('BlackBox\Storage');
        $this->storage              = new StorageCache($this->sourceStorageMock, $this->storageCacheMock);
    }

    public function tearDown()
    {
        unset(
            $this->sourceStorageMock,
            $this->storageCacheMock,
            $this->storage
        );
    }

    /**
     * @test
     */
    public function gets_data_directly_from_cache()
    {
        $cachedData = 'cache';

        $this->storageCacheMock->expects($this->once())
        ->method('get')
        ->will($this->returnValue($cachedData));

        $this->sourceStorageMock->expects($this->never())
        ->method('get');

        $this->assertEquals($cachedData, $this->storage->get(25), 'The cache is not returning the cached data');
    }

    /**
     * @test
     */
    public function gets_data_from_source_when_not_in_cache()
    {
        $sourceData = 'source_storage_data';

        $this->storageCacheMock->expects($this->once())
        ->method('get')
        ->will($this->returnValue(null));

        $this->sourceStorageMock->expects($this->once())
        ->method('get')
        ->will($this->returnValue($sourceData));

        $this->assertEquals(
            $sourceData,
            $this->storage->get(26),
            'The value of the source storage is not being returned'
        );
    }

    /**
     * @test
     */
    public function set_stores_same_data_in_both_storages()
    {
        $id     = 25;
        $data   = 'data_to_set';

        $this->storageCacheMock->expects($this->once())
            ->method('set')
            ->with($id, $data);

        $this->sourceStorageMock->expects($this->once())
            ->method('set')
            ->with($id, $data);

        $this->storage->set($id, $data);
    }

    /**
     * @test
     */
    public function adds_data_to_cache_and_calls_set_in_source_storage()
    {
        $cachedDataId = 'id';

        $this->storageCacheMock->expects($this->once())
            ->method('add')
            ->will($this->returnValue($cachedDataId));

        $this->sourceStorageMock->expects($this->once())
            ->method('set')
            ->with($cachedDataId, $this->anything());

        $generatedId = $this->storage->add('data_to_be_added');

        $this->assertEquals($cachedDataId, $generatedId, 'The id generated after calling add is not correct');
    }

    /**
     * @test
     */
    public function removes_data_from_both_storages()
    {
        $this->sourceStorageMock->expects($this->once())
            ->method('remove');

        $this->storageCacheMock->expects($this->once())
            ->method('remove');

        $this->storage->remove('id_to_remove');
    }

    /**
     * @test
     */
    public function get_iterator_from_source_storage()
    {
        $iterator = 'an_iterator';

        $this->sourceStorageMock->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue($iterator));

        $this->assertEquals($iterator, $this->storage->getIterator(), 'The source storage iterator must be returned');
    }
}
