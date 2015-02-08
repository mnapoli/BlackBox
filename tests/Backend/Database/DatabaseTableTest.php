<?php

namespace Tests\BlackBox\Backend\Database;

use BlackBox\Backend\Database\DatabaseTable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\TableDiff;
use Doctrine\DBAL\Types\Type;

/**
 * @covers \BlackBox\Backend\Database\DatabaseTable
 */
class DatabaseTableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var DatabaseTable
     */
    private $storage;

    public function setUp()
    {
        $this->connection = $this->createDbConnection();
        $this->connection->insert('foo', [
            '_id'  => '1',
            'name' => 'John',
        ]);

        $this->storage = new DatabaseTable($this->connection, 'foo');
    }

    /**
     * @test
     */
    public function get_should_return_a_row()
    {
        $expected = [
            'name' => 'John',
        ];

        $this->assertEquals($expected, $this->storage->get('1'));
    }

    /**
     * @test
     */
    public function get_missing_row_should_return_null()
    {
        $this->assertNull($this->storage->get('2'));
    }

    /**
     * @test
     */
    public function set_should_insert()
    {
        $data = [
            'name' => 'John',
        ];
        $this->storage->set('2', $data);

        $row = $this->connection->fetchAssoc('SELECT * FROM foo WHERE _id = "2"');

        $data['_id'] = '2';
        $this->assertEquals($data, $row);
    }

    /**
     * @test
     * @depends set_should_insert
     */
    public function set_existing_row_should_update()
    {
        $data = [
            'name' => 'Doe',
        ];
        $this->storage->set('2', $data);

        $row = $this->connection->fetchAssoc('SELECT * FROM foo WHERE _id = "2"');

        $data['_id'] = '2';
        $this->assertEquals($data, $row);
    }

    /**
     * @test
     */
    public function set_new_column_should_create_it()
    {
        $data = [
            'email' => 'john@microsoft.com',
        ];
        $this->storage->set('2', $data);

        $row = $this->connection->fetchAssoc('SELECT * FROM foo WHERE _id = "2"');

        $data['_id'] = '2';
        $data['name'] = null;
        $this->assertEquals($data, $row);
    }

    /**
     * @test
     */
    public function set_should_escape_array_keys()
    {
        $data = [
            'hax0r\' foo' => 'hax0r" foo',
        ];
        $this->storage->set('2', $data);

        $row = $this->connection->fetchAssoc('SELECT * FROM foo WHERE _id = "2"');

        $expected = [
            '_id' => '2',
            'name' => null,
            'hax0r\' foo' => 'hax0r" foo',
        ];
        $this->assertEquals($expected, $row);
    }

    /**
     * @test
     */
    public function set_null_should_delete_row()
    {
        $this->storage->set('1', null);

        $row = $this->connection->fetchAssoc('SELECT * FROM foo WHERE _id = "1"');
        $this->assertFalse($row);
    }

    /**
     * @test
     */
    public function set_null_on_unknown_id_should_not_throw_an_exception()
    {
        $this->storage->set('123', null);
    }

    /**
     * @test
     */
    public function it_should_be_traversable()
    {
        $this->storage->set('2', [
            'email' => 'john@microsoft.com',
        ]);

        $expected = [
            '1' => [
                'name' => 'John',
                'email' => null,
            ],
            '2' => [
                'name' => null,
                'email' => 'john@microsoft.com',
            ],
        ];

        $this->assertEquals($expected, iterator_to_array($this->storage));
    }

    /**
     * @return Connection
     */
    private function createDbConnection()
    {
        $connection = DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ]);

        DatabaseTable::createTable($connection, 'foo');

        $diff = new TableDiff('foo');
        $diff->addedColumns[] = new Column('name', Type::getType(Type::STRING), ['notnull' => false]);
        $connection->getSchemaManager()->alterTable($diff);

        return $connection;
    }
}
