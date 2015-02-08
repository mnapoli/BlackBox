<?php

namespace Tests\BlackBox\Backend;

use BlackBox\Backend\ArrayStorage;
use Tests\BlackBox\BaseStorageTest;

/**
 * @covers \BlackBox\Backend\ArrayStorage
 */
class ArrayStorageTest extends BaseStorageTest
{
    public function setUp()
    {
        parent::setUp();

        $this->storage = new ArrayStorage();
    }
}
