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

- the **`MapStorage`** interface represents a key-value store:

```php
namespace BlackBox;

interface MapStorage extends Traversable
{
    public function get($id);
    public function set($id, $data);
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
- [Redis](http://redis.io/)
- PHP arrays (i.e. in memory)

Data can optionally be:

- stored in JSON
- stored in YAML
- serialized using PHP's `serialize()` function
- encrypted with AES

An integration with the [JMS Serializer](http://jmsyst.com/libs/serializer) library also allows to serialize PHP objects to JSON, XML or YAML.

## Backends

Backends are classes that implement `MapStorage`:

- `FileStorage`
- `MultipleFileStorage`
- `RedisStorage`
- `DatabaseTable`
- `ArrayStorage`

You can read all about backends in the [Backends documentation](doc/backends.md).

## Transformers

Transformers transform data before storage and after retrieval:

- `JsonEncoder`
- `YamlEncoder`
- `PhpSerializerEncoder`
- `ObjectArrayMapper`
- `AesEncrypter`
- `JmsSerializer` for using the [JMS Serializer library](http://jmsyst.com/libs/serializer)

You can read all about transformers in the [Transformers documentation](doc/transformers.md).

```php
// Store data in files
$storage = new MapWithTransformers(
    new MultipleFileStorage('some/directory')
);

// Map objects to array and vice-versa
// (because JSON can't deserialize arrays into PHP objects of a specific class)
$storage->addTransformer(new ObjectArrayMapper('MyClass'));

// Encode the data in JSON
$storage->addTransformer(new JsonEncoder());

$object = new MyClass();
$object->name = 'Lebowski';

$storage->set('foo', $object);
// will turn the object into an array
// then will encode it in JSON
// then will store it into a file

$object = $storage->get('foo');
// will read from the file
// then will decode the JSON
// then will map the decoded array to a MyClass object

echo $object->name; // Lebowski
```

## License

BlackBox is released under the MIT license.
