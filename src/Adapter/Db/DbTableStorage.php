<?php

namespace BlackBox\Adapter\Db;

use BlackBox\Exception\StorageException;
use BlackBox\MapStorage;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\TableDiff;
use Doctrine\DBAL\Types\Type;

/**
 * Stores data in a database table.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class DbTableStorage implements MapStorage
{
    const COLUMN_ID = '_id';

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $tableName;

    public function __construct(Connection $connection, $tableName)
    {
        $this->connection = $connection;
        $this->tableName = (string) $tableName;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $query = sprintf(
            'SELECT * FROM %s',
            $this->connection->quoteIdentifier($this->tableName)
        );

        try {
            $rows = $this->connection->fetchAll($query);
        } catch (DBALException $e) {
            throw DatabaseException::fromDBALException($e);
        }

        $data = [];

        foreach ($rows as $row) {
            $id = $row[self::COLUMN_ID];

            $data[$id] = $this->rowToData($row);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     * @param array $data
     */
    public function setData($data)
    {
        // TODO Optimize
        foreach ($data as $id => $value) {
            $this->set($id, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $query = sprintf(
            'SELECT * FROM %s WHERE %s = ?',
            $this->connection->quoteIdentifier($this->tableName),
            $this->connection->quoteIdentifier(self::COLUMN_ID)
        );

        try {
            $row = $this->connection->fetchAssoc($query, [$id]);
        } catch (DBALException $e) {
            throw DatabaseException::fromDBALException($e);
        }

        if ($row === false) {
            return null;
        }

        return $this->rowToData($row);
    }

    /**
     * {@inheritdoc}
     */
    public function set($id, $data)
    {
        if ($data === null) {
            // TODO
            throw new \RuntimeException('TODO');
        }

        $data = $this->quoteColumns($data);

        try {
            $this->createMissingColumns($data);

            if (! $this->hasRow($id)) {
                // Insert
                $data[self::COLUMN_ID] = $id;
                $this->connection->insert($this->tableName, $data);
            } else {
                // Update
                $this->connection->update($this->tableName, $data, [self::COLUMN_ID => $id]);
            }
        } catch (DBALException $e) {
            throw DatabaseException::fromDBALException($e);
        }
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @return Connection
     */
    public function getDbConnection()
    {
        return $this->connection;
    }

    private function hasRow($id)
    {
        $query = sprintf(
            'SELECT COUNT(*) FROM %s WHERE %s = ?',
            $this->connection->quoteIdentifier($this->tableName),
            $this->connection->quoteIdentifier(self::COLUMN_ID)
        );
        $count = $this->connection->fetchColumn($query, [$id], 0);

        return $count >= 1;
    }

    /**
     * Quote the column names.
     * @param array $data
     * @return array
     */
    private function quoteColumns(array $data)
    {
        $safeData = [];
        foreach ($data as $key => $value) {
            $key = $this->connection->quoteIdentifier($key);
            $safeData[$key] = $value;
        }
        return $safeData;
    }

    private function createMissingColumns($data)
    {
        $table = $this->connection->getSchemaManager()->listTableDetails($this->tableName);

        $diff = new TableDiff($this->tableName);

        foreach ($data as $key => $value) {
            if ($table->hasColumn($key)) {
                continue;
            }

            switch (gettype($value)) {
                case 'boolean':
                    $type = Type::BOOLEAN;
                    break;
                case 'integer':
                    $type = Type::INTEGER;
                    break;
                case 'double':
                    $type = Type::FLOAT;
                    break;
                case 'string':
                    $type = Type::STRING;
                    break;
                default:
                    throw new StorageException(sprintf(
                        'Unable to create a new column "%s" in table %s because variables of type %s can\'t be handled',
                        $this->tableName,
                        $key,
                        gettype($value)
                    ));
            }

            $diff->addedColumns[] = new Column($key, Type::getType($type), ['notnull' => false]);
        }

        if (! empty($diff->addedColumns)) {
            $this->connection->getSchemaManager()->alterTable($diff);
        }
    }

    /**
     * @param array $row
     * @return array
     */
    private function rowToData($row)
    {
        unset($row[self::COLUMN_ID]);

        return $row;
    }
}
