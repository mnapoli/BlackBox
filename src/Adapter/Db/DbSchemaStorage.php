<?php

namespace BlackBox\Adapter\Db;

use BlackBox\Exception\StorageException;
use BlackBox\MapStorage;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;

/**
 * Database schema.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class DbSchemaStorage implements MapStorage
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var DbTableStorage[]
     */
    private $tables = [];

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $tables = $this->getTableNames();

        $data = [];

        foreach ($tables as $table) {
            $data[$table] = $this->get($table);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function setData($data)
    {
        foreach ($data as $id => $value) {
            $this->set($id, $value);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return DbTableStorage
     */
    public function get($id)
    {
        if (! $this->hasTable($id)) {
            return null;
        }

        if (! isset($this->tables[$id])) {
            $this->tables[$id] = new DbTableStorage($this->connection, $id);
        }

        return $this->tables[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function set($id, $data)
    {
        if ($data === null) {
            $this->dropTable($id);
            return;
        }

        if ($this->hasTable($id)) {
            throw new StorageException(sprintf('The table "%s" already exist', $id));
        }

        $table = new Table($id, [
            new Column(DbTableStorage::COLUMN_ID, Type::getType(Type::STRING)),
        ]);
        $table->setPrimaryKey([DbTableStorage::COLUMN_ID]);

        $this->getSchema()->createTable($table);

        // TODO add data to the table
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
}
