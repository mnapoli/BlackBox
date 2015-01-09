---
currentMenu: backends
---

Backends are classes that implement the `Storage` or `MapStorage` interfaces.

## `FileStorage`

*Implements `Storage`.*

Stores data in a single file.

```php
$storage = new FileStorage('some/file.txt');
$storage->setData('Hello world!');
```

## `MultipleFileStorage`

*Implements `MapStorage`.*

Stores data in multiple files (one file per ID).

```php
$storage = new MultipleFileStorage('some/writable/directory', $extension = 'txt');
```

File names are constructed from the ids. If `$extension` is provided, then it is used as file extension.

## `RedisStorage`

*Implements `MapStorage`.*

Stores data in a Redis server.

```php
$storage = RedisStorage::create();
```

The Redis backend requires Predis to be installed in your project:

```
composer require predis/predis
```

You can customize the options of the Redis connection:

```php
$storage = RedisStorage::create($parameters, $options);

// or manually creating the Redis instance:
$redis = new \Predis\Client($parameters, $options);
$storage = new RedisStorage($redis);
```

Check out the [Predis documentation](https://github.com/nrk/predis) for more information on the connection parameters.

## `DatabaseTable`

*Implements `MapStorage`.*

Stores data in a Database table. This backend relies on the [**Doctrine DBAL**](http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/) library, a powerful Database Abstraction Layer which allows to connect to MySQL, PostgreSQL, SQLite, Oracle, …

This backend is actually composed of two classes:

- `DatabaseSchema`: map to retrieve (or create) database tables
- `DatabaseTable`: store and retrieve table rows

Both classes implement `MapStorage`.

You can use `DatabaseTable` alone or use `DatabaseSchema` to create and retrieve table instances.

To use this backend, you need to install the Doctrine DBAL library:

```
composer require doctrine/dbal
```

Here is a simple example using a MySQL server:

```php
$dbal = DriverManager::getConnection([
    'dbname'   => 'my_database',
    'user'     => 'user',
    'password' => 'secret',
    'host'     => 'localhost',
    'driver'   => 'pdo_mysql',
]);
$dbStorage = new DatabaseSchema($dbal);

// Returns the 'users' table
$userStorage = $dbStorage->get('users');

$userStorage->set('johndoe', $johnDoe);
$user = $userStorage->get('johndoe');
```

Each table contains a `_id` string primary key. Be aware that BlackBox is not meant to be a full database abstraction layer: you cannot use auto-increment, you cannot perform SQL queries, …

Note also that the Database storage backend *is not an ORM*. You cannot store objects directly in a `TableStorage`, you can only store arrays containing primitive types. You can however use transformers to turn objects into arrays (and vice-versa when data is retrieved).

See all the configuration options in the [DBAL configuration documentation](http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html).

## `MemoryStorage`

*Implements `Storage`.*

Stores data in memory. Obviously the data is not persistent between requests.
This backend can be useful for tests or quick prototyping.

## `ArrayStorage`

*Implements `MapStorage`.*

Stores data in an array in memory. Obviously the data is not persistent between requests.
This backend can be useful for tests or quick prototyping.
