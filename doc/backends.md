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

## `MemoryStorage`

*Implements `Storage`.*

Stores data in memory. Obviously the data is not persistent between requests.
This backend can be useful for tests or quick prototyping.

## `ArrayStorage`

*Implements `MapStorage`.*

Stores data in an array in memory. Obviously the data is not persistent between requests.
This backend can be useful for tests or quick prototyping.
