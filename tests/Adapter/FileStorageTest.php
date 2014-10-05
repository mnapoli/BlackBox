<?php

namespace Tests\BlackBox\Transformer;

use BlackBox\Adapter\FileStorage;

/**
 * @covers \BlackBox\Adapter\FileStorage
 */
class FileStorageTest extends \PHPUnit_Framework_TestCase
{
    private $file;

    protected function setUp()
    {
        parent::setUp();
        $this->file = __DIR__ . '/../tmp/foo';
        if (file_exists($this->file)) {
            unlink($this->file);
        }
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
    public function it_should_store_data()
    {
        $storage = new FileStorage($this->file);

        $storage->setData('foo');

        $this->assertEquals('foo', file_get_contents($this->file));
        $this->assertEquals('foo', $storage->getData());
    }

    /**
     * @test
     */
    public function it_should_handle_non_existing_files()
    {
        $storage = new FileStorage($this->file);

        $this->assertNull($storage->getData());
    }
}
