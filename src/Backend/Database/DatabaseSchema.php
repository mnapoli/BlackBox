<?php

namespace BlackBox\Backend\Database;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;

/**
 * Database schema.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class DatabaseSchema
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
     * @param string $name
     * @return DatabaseTable
     */
    public function getTable($name)
    {
        if (! isset($this->tables[$name])) {
            $this->tables[$name] = new DatabaseTable($this->connection, $name);
        }

        return $this->tables[$name];
    }

    public function createTable($name)
    {
        try {
            $table = new Table($name, [
                new Column(DatabaseTable::COLUMN_ID, Type::getType(Type::INTEGER), [
                    'primary'       => true,
                    'autoincrement' => true,
                    'notnull'       => true,
                ]),
            ]);

            $this->getSchema()->createTable($table);
        } catch (DBALException $e) {
            throw DatabaseException::fromDBALException($e);
        }
    }

    public function dropTable($name)
    {
        $this->getSchema()->dropTable($name);

        if (isset($this->tables[$name])) {
            unset($this->tables[$name]);
        }
    }

    public function getTables()
    {
        $tables = $this->getTableNames();

        $data = [];

        foreach ($tables as $table) {
            $data[$table] = $this->getTable($table);
        }

        return $data;
    }

    public function getTableNames()
    {
        return $this->getSchema()->listTableNames();
    }

    private function getSchema()
    {
        return $this->connection->getSchemaManager();
    }
}
