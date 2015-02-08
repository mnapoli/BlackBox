<?php

namespace Tests\BlackBox\Transformer;

use BlackBox\Backend\FileStorage;
use BlackBox\Transformer\JsonEncoder;
use Tests\BlackBox\BaseStorageTest;

/**
 * @covers \BlackBox\Backend\FileStorage
 */
class FileStorageTest extends BaseStorageTest
{
    private $file;

    /**
     * @var FileStorage
     */
    protected $storage;

    protected function setUp()
    {
        parent::setUp();

        $this->file = __DIR__ . '/../tmp/foo';
        if (file_exists($this->file)) {
            unlink($this->file);
        }

        $this->storage = new FileStorage($this->file, new JsonEncoder());
    }

    protected function tearDown()
    {
        parent::tearDown();

        if (file_exists($this->file)) {
            unlink($this->file);
        }
    }

    /**
     * @test
     */
    public function it_should_store_data_as_map()
    {
        parent::it_should_store_data_as_map();

        $this->assertEquals('{"foo":"bar"}', file_get_contents($this->file));
    }

    /**
     * @test
     */
    public function set_null_should_store_null()
    {
        parent::set_null_should_store_null();

        $this->assertEquals('{"foo":null}', file_get_contents($this->file));
    }
}
