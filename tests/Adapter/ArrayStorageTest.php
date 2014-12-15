<?php

namespace Tests\BlackBox\Adapter;

use BlackBox\Adapter\ArrayStorage;

/**
 * @covers \BlackBox\Adapter\ArrayStorage
 */
class ArrayStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_store_data_as_map()
    {
        $storage = new ArrayStorage();

        $storage->set('foo', 'bar');

        $this->assertEquals('bar', $storage->get('foo'));
    }

    /**
     * @test
     */
    public function it_should_be_traversable()
    {
        $storage = new ArrayStorage();

        $this->assertEquals([], iterator_to_array($storage));

        $storage->set('foo', 'bar');

        $this->assertEquals([ 'foo' => 'bar' ], iterator_to_array($storage));
    }
}
