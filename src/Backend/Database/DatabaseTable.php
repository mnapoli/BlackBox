<?php

namespace BlackBox\Backend\Database;

use BlackBox\Storage;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Schema\TableDiff;
use Doctrine\DBAL\Types\Type;
use IteratorAggregate;

/**
 * Stores data in a database table.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class DatabaseTable implements IteratorAggregate, Storage
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

    public function get(string $id)
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

    public function set(string $id, $data) : void
    {
        if ($data === null) {
            $this->remove($id);
            return;
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

    public function remove(string $id) : void
    {
        try {
            $this->connection->delete($this->tableName, [self::COLUMN_ID => $id]);
        } catch (DBALException $e) {
            throw DatabaseException::fromDBALException($e);
        }
    }

    public function getIterator()
    {
        // TODO optimize
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

        yield from $data;
    }

    public function getTableName() : string
    {
        return $this->tableName;
    }

    public function getDbConnection() : Connection
    {
        return $this->connection;
    }

    private function hasRow(string $id) : bool
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
     */
    private function quoteColumns(array $data) : array
    {
        $safeData = [];
        foreach ($data as $key => $value) {
            $key = $this->connection->quoteIdentifier($key);
            $safeData[$key] = $value;
        }
        return $safeData;
    }

    private function createMissingColumns($data) : void
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
                    throw new DatabaseException(sprintf(
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

    private function rowToData(array $row) : array
    {
        unset($row[self::COLUMN_ID]);

        return $row;
    }

    public static function createTable(Connection $connection, string $tableName) : void
    {
        $table = new Table($tableName, [
            new Column(DatabaseTable::COLUMN_ID, Type::getType(Type::STRING), [
                'primary' => true,
                'notnull' => true,
            ]),
        ]);

        try {
            $connection->getSchemaManager()->createTable($table);
        } catch (DBALException $e) {
            throw DatabaseException::fromDBALException($e);
        }
    }
}
