<?php

namespace Tests\BlackBox\Backend\Database;

use BlackBox\Backend\Database\DatabaseSchema;
use BlackBox\Backend\Database\DatabaseTable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;

/**
 * @covers \BlackBox\Backend\Database\DatabaseSchema
 */
class DatabaseSchemaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var DatabaseSchema
     */
    private $storage;

    public function setUp()
    {
        $this->connection = DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ]);
        $this->connection->getSchemaManager()->createTable(new Table('foo', [
            new Column('_id', Type::getType(Type::STRING)),
        ]));

        $this->storage = new DatabaseSchema($this->connection);
    }

    /**
     * @test
     */
    public function get_should_return_a_table_storage()
    {
        $table = $this->storage->get('foo');

        $this->assertTrue($table instanceof DatabaseTable);
        $this->assertEquals('foo', $table->getTableName());
    }

    /**
     * @test
     */
    public function get_should_return_null_on_missing_table()
    {
        $this->assertNull($this->storage->get('bar'));
    }

    /**
     * @test
     */
    public function set_should_add_a_new_table_storage()
    {
        $this->storage->set('bar', new \BlackBox\Backend\Database\DatabaseTable($this->connection, 'bar'));

        $tables = $this->connection->getSchemaManager()->listTableNames();
        $this->assertContains('bar', $tables);

        $tableStorage = $this->storage->get('bar');
        $this->assertTrue($tableStorage instanceof \BlackBox\Backend\Database\DatabaseTable);
        $this->assertEquals('bar', $tableStorage->getTableName());

        $table = $this->connection->getSchemaManager()->listTableDetails('bar');
        $columns = $table->getColumns();
        $this->assertCount(1, $columns);
        $column = current($columns);
        $this->assertEquals('_id', $column->getName());
        $this->assertEquals(Type::STRING, $column->getType()->getName());
    }

    /**
     * @test
     */
    public function it_should_be_traversable()
    {
        $array = iterator_to_array($this->storage);
        $this->assertCount(1, $array);
        $this->assertArrayHasKey('foo', $array);
        $this->assertTrue($array['foo'] instanceof DatabaseTable);
    }

    /**
     * @test
     */
    public function set_null_should_drop_the_table()
    {
        $this->storage->set('foo', null);

        $tables = $this->connection->getSchemaManager()->listTableNames();
        $this->assertEmpty($tables);
        $this->assertNull($this->storage->get('foo'));
    }
}
