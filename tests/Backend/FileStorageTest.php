<?php

namespace Tests\BlackBox\Transformer;

use BlackBox\Backend\FileStorage;
use BlackBox\Transformer\JsonEncoder;

/**
 * @covers \BlackBox\Backend\FileStorage
 */
class FileStorageTest extends \PHPUnit_Framework_TestCase
{
    private $file;

    /**
     * @var FileStorage
     */
    private $storage;

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
        $this->storage->set('foo', 'bar');

        $this->assertEquals('{"foo":"bar"}', file_get_contents($this->file));
        $this->assertEquals('bar', $this->storage->get('foo'));
    }

    /**
     * @test
     */
    public function it_should_be_traversable()
    {
        $this->assertEquals([], iterator_to_array($this->storage));

        $this->storage->set('foo', 'bar');

        $this->assertEquals([ 'foo' => 'bar' ], iterator_to_array($this->storage));
    }

    /**
     * @test
     */
    public function set_null_should_delete()
    {
        $this->storage->set('foo', 'bar');
        $this->storage->set('foo', null);

        $this->assertNull($this->storage->get('foo'));
        $this->assertEquals('[]', file_get_contents($this->file));
    }

    /**
     * @test
     */
    public function get_non_existent_key_should_return_null()
    {
        $this->assertNull($this->storage->get('foo'));
    }
}
