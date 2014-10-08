<?php

namespace BlackBox\Adapter\Db;

use BlackBox\Exception\StorageException;
use Doctrine\DBAL\DBALException;

/**
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class DatabaseException extends StorageException
{
    public static function fromDBALException(DBALException $e)
    {
        return new self($e->getMessage(), 0, $e);
    }
}
