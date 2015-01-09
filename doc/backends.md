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

## `MemoryStorage`

*Implements `Storage`.*

Stores data in memory. Obviously the data is not persistent between requests.
This backend can be useful for tests or quick prototyping.

## `ArrayStorage`

*Implements `MapStorage`.*

Stores data in an array in memory. Obviously the data is not persistent between requests.
This backend can be useful for tests or quick prototyping.
