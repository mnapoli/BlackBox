<?php

namespace BlackBox\Backend\Database;

use ArrayIterator;
use BlackBox\MapStorage;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use IteratorAggregate;

/**
 * Database schema.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class DatabaseSchema implements IteratorAggregate, MapStorage
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var DatabaseTable[]
     */
    private $tables = [];

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     *
     * @return DatabaseTable
     */
    public function get($id)
    {
        if (! $this->hasTable($id)) {
            return null;
        }

        if (! isset($this->tables[$id])) {
            $this->tables[$id] = new DatabaseTable($this->connection, $id);
        }

        return $this->tables[$id];
    }

    /**
     * {@inheritdoc}
     *
     * @param DatabaseTable|null $data
     */
    public function set($id, $data)
    {
        if ($data === null) {
            $this->dropTable($id);
            return;
        }

        if (! $data instanceof DatabaseTable) {
            throw new \InvalidArgumentException('You can only set DatabaseTable objects in a DatabaseSchema');
        }

        $this->assertTableDoesNotExist($id);
        $this->createTable($id);

        $this->tables[$id] = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->getTables());
    }

    private function hasTable($name)
    {
        return in_array($name, $this->getTableNames());
    }

    private function getTableNames()
    {
        return $this->getSchema()->listTableNames();
    }

    private function getSchema()
    {
        return $this->connection->getSchemaManager();
    }

    private function dropTable($name)
    {
        if (! $this->hasTable($name)) {
            return;
        }

        $this->getSchema()->dropTable($name);

        if (isset($this->tables[$name])) {
            unset($this->tables[$name]);
        }
    }

    private function assertTableDoesNotExist($name)
    {
        if ($this->hasTable($name)) {
            throw new DatabaseException(sprintf('The table "%s" already exist', $name));
        }
    }

    private function createTable($id)
    {
        try {
            $table = new Table($id, [
                new Column(DatabaseTable::COLUMN_ID, Type::getType(Type::STRING)),
            ]);
            $table->setPrimaryKey([DatabaseTable::COLUMN_ID]);

            $this->getSchema()->createTable($table);
        } catch (DBALException $e) {
            throw DatabaseException::fromDBALException($e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTables()
    {
        $tables = $this->getTableNames();

        $data = [];

        foreach ($tables as $table) {
            $data[$table] = $this->get($table);
        }

        return $data;
    }
}
