<?php

namespace Tests\BlackBox;

use BlackBox\Storage;

/**
 * Base test that specifies the behavior of storages.
 */
abstract class BaseStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Storage
     */
    protected $storage;

    /**
     * @test
     */
    public function it_should_store_data()
    {
        $this->storage->set('foo', 'bar');

        $this->assertEquals('bar', $this->storage->get('foo'));
    }

    /**
     * @test
     */
    public function get_non_existent_key_should_return_null()
    {
        $this->assertSame(null, $this->storage->get('foo'));
    }

    /**
     * @test
     */
    public function set_null_should_store_null()
    {
        $this->storage->set('foo', 'bar');
        $this->storage->set('foo', null);
        $this->assertSame(null, $this->storage->get('foo'));
    }

    /**
     * @test
     */
    public function remove_should_delete_the_entry()
    {
        $this->storage->set('foo', 'bar');
        $this->storage->remove('foo');
        $this->assertSame(null, $this->storage->get('foo'));
    }

    /**
     * @test
     */
    public function it_should_be_traversable()
    {
        $this->assertEquals([], iterator_to_array($this->storage));

        $this->storage->set('foo', 'test 1');
        $this->storage->set('bar', 'test 2');

        $expected = [
            'foo' => 'test 1',
            'bar' => 'test 2',
        ];

        $this->assertEquals($expected, iterator_to_array($this->storage));
    }
}
