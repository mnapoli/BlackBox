---
currentMenu: home
---

# BlackBox

BlackBox is a storage library that abstracts backends and data transformation behind simple interfaces.

[![Build Status](https://img.shields.io/travis/mnapoli/BlackBox.svg?style=flat-square)](https://travis-ci.org/mnapoli/BlackBox)
[![Coverage Status](https://img.shields.io/coveralls/mnapoli/BlackBox/master.svg?style=flat-square)](https://coveralls.io/r/mnapoli/BlackBox?branch=master)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/mnapoli/BlackBox.svg?style=flat-square)](https://scrutinizer-ci.com/g/mnapoli/BlackBox/?branch=master)
[![Latest Version](https://img.shields.io/github/release/mnapoli/BlackBox.svg?style=flat-square)](https://packagist.org/packages/mnapoli/BlackBox)

> Store data. "Where" and "how" can be decided later.

**Experimental: this project is still at the state of experimentation. Use with caution.**

## Usage

The API is defined by interfaces and is extremely simple.

```php
namespace BlackBox;

interface Storage extends Traversable
{
    public function get($id);
    public function set($id, $data);
    public function remove($id);
}

$storage->set('foo', 'Hello World!');

echo $storage->get('foo'); // Hello World!

foreach ($storage as $key => $item) {
    echo $key; // foo
    echo $item; // Hello World!
}
```

You can read all about those interfaces in the [Interfaces documentation](doc/interfaces.md).

## Features

BlackBox can store data in:

- files
- database (MySQL, PostgreSQL, SQLite, Oracle, …) using [Doctrine DBAL](http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/)
- PHP arrays (i.e. in memory)

Data can optionally be:

- stored in JSON
- stored in YAML
- encrypted with AES

Additionally a storage can be cached with another (e.g. cache a DB storage with a Redis or array storage).

## Backends

Backends are classes that implement `Storage`:

- `FileStorage`
- `DirectoryStorage`
- `DatabaseTable`
- `ArrayStorage`

You can read all about backends in the [Backends documentation](doc/backends.md).

## Transformers

Transformers transform data before storage and after retrieval:

- `JsonEncoder`
- `YamlEncoder`
- `ObjectArrayMapper`
- `AesEncrypter`

You can read all about transformers in the [Transformers documentation](doc/transformers.md).

```php
// Encode the data in JSON
$storage = new JsonEncoder(
    // Store data in files
    new DirectoryStorage('some/directory')
);

$storage->set('foo', [
    'name' => 'Lebowski',
]);
// will encode it in JSON
// then will store it into a file

$data = $storage->get('foo');
// will read from the file
// then will decode the JSON

echo $data['name']; // Lebowski
```

## License

BlackBox is released under the MIT license.
