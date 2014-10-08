<?php

namespace Tests\BlackBox\Adapter\Db;

use BlackBox\Adapter\Db\DbSchemaStorage;
use BlackBox\Adapter\Db\DbTableStorage;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;

/**
 * @covers \BlackBox\Adapter\Db\DbSchemaStorage
 */
class DbSchemaStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function get_should_return_a_table_storage()
    {
        $storage = new DbSchemaStorage($this->assert_db_connection());

        $table = $storage->get('foo');

        $this->assertTrue($table instanceof DbTableStorage);
        $this->assertEquals('foo', $table->getTableName());
    }

    /**
     * @test
     */
    public function get_should_return_null_on_missing_table()
    {
        $storage = new DbSchemaStorage($this->assert_db_connection());
        $this->assertNull($storage->get('bar'));
    }

    /**
     * @test
     */
    public function set_should_add_a_new_table_storage()
    {
        $connection = $this->assert_db_connection();
        $storage = new DbSchemaStorage($connection);

        $storage->set('bar', []);

        $tables = $connection->getSchemaManager()->listTableNames();
        $this->assertContains('bar', $tables);

        $tableStorage = $storage->get('bar');
        $this->assertTrue($tableStorage instanceof DbTableStorage);
        $this->assertEquals('bar', $tableStorage->getTableName());

        $table = $connection->getSchemaManager()->listTableDetails('bar');
        $columns = $table->getColumns();
        $this->assertCount(1, $columns);
        $column = current($columns);
        $this->assertEquals('_id', $column->getName());
        $this->assertEquals(Type::STRING, $column->getType()->getName());
    }

    /**
     * @test
     */
    public function set_null_should_drop_the_table()
    {
        $connection = $this->assert_db_connection();
        $storage = new DbSchemaStorage($connection);

        $storage->set('foo', null);

        $tables = $connection->getSchemaManager()->listTableNames();
        $this->assertEmpty($tables);
        $this->assertNull($storage->get('foo'));
    }

    /**
     * @return Connection
     */
    private function assert_db_connection()
    {
        $connection = DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ]);

        $table = new Table('foo', [
            new Column('_id', Type::getType(Type::STRING)),
        ]);

        $connection->getSchemaManager()->createTable($table);

        return $connection;
    }
}