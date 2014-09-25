<?php

namespace Tests\BlackBox\Adapter\Db;

use BlackBox\Adapter\Db\DbTableStorage;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;

/**
 * @covers \BlackBox\Adapter\Db\DbTableStorage
 */
class DbTableStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function get_should_return_a_row()
    {
        $storage = $this->assertDbTableStorage();

        $expected = [
            'name' => 'John',
        ];

        $this->assertEquals($expected, $storage->get('1'));
    }

    /**
     * @test
     */
    public function get_missing_row_should_return_null()
    {
        $storage = $this->assertDbTableStorage();

        $this->assertNull($storage->get('2'));
    }

    /**
     * @test
     */
    public function set_should_insert()
    {
        $storage = $this->assertDbTableStorage();

        $data = [
            'name' => 'John',
        ];
        $storage->set('2', $data);

        $row = $storage->getDbConnection()->fetchAssoc('SELECT * FROM foo WHERE _id = "2"');

        $data['_id'] = '2';
        $this->assertEquals($data, $row);

        return $storage;
    }

    /**
     * @test
     * @depends set_should_insert
     */
    public function set_existing_row_should_update(DbTableStorage $storage)
    {
        $data = [
            'name' => 'Doe',
        ];
        $storage->set('2', $data);

        $row = $storage->getDbConnection()->fetchAssoc('SELECT * FROM foo WHERE _id = "2"');

        $data['_id'] = '2';
        $this->assertEquals($data, $row);
    }

    /**
     * @test
     */
    public function set_new_column_should_create_it()
    {
        $storage = $this->assertDbTableStorage();

        $data = [
            'email' => 'john@microsoft.com',
        ];
        $storage->set('2', $data);

        $row = $storage->getDbConnection()->fetchAssoc('SELECT * FROM foo WHERE _id = "2"');

        $data['_id'] = '2';
        $data['name'] = null;
        $this->assertEquals($data, $row);
    }

    /**
     * @test
     */
    public function set_should_escape_array_keys()
    {
        $storage = $this->assertDbTableStorage();

        $data = [
            'hax0r\' foo' => 'hax0r" foo',
        ];
        $storage->set('2', $data);

        $row = $storage->getDbConnection()->fetchAssoc('SELECT * FROM foo WHERE _id = "2"');

        $expected = [
            '_id' => '2',
            'name' => null,
            'hax0r\' foo' => 'hax0r" foo',
        ];
        $this->assertEquals($expected, $row);
    }

    /**
     * @return DbTableStorage
     */
    private function assertDbTableStorage()
    {
        $db = $this->assertDbConnection();
        $db->insert('foo', [
            '_id'  => '1',
            'name' => 'John',
        ]);

        $storage = new DbTableStorage($db, 'foo');

        return $storage;
    }

    /**
     * @return Connection
     */
    private function assertDbConnection()
    {
        $connection = DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ]);

        $table = new Table('foo', [
            new Column('_id', Type::getType(Type::STRING)),
            new Column('name', Type::getType(Type::STRING), ['notnull' => false]),
        ]);
        $table->setPrimaryKey(['_id']);

        $connection->getSchemaManager()->createTable($table);

        return $connection;
    }
}
