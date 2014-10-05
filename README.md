---
currentMenu: home
---

# BlackBox

BlackBox is a storage library that abstracts backends and data transformation behind simple interfaces.

[![Build Status](https://travis-ci.org/mnapoli/BlackBox.svg?branch=master)](https://travis-ci.org/mnapoli/BlackBox)
[![Coverage Status](https://img.shields.io/coveralls/mnapoli/BlackBox.svg)](https://coveralls.io/r/mnapoli/BlackBox?branch=master)

Store data. "Where" and "how" can be decided later.

## Usage

The API is defined by interfaces and is extremely simple.

You can have 2 storage types:

- the basic **`Storage`** interface for storing just 1 thing:

```php
namespace BlackBox;

interface Storage
{
    public function getData();
    public function setData($data);
}

$storage->setData('Hello World!');

echo $storage->getData(); // Hello World!
```

- the **`MapStorage`** interface for storing several items:

```php
namespace BlackBox;

interface MapStorage extends Storage
{
    public function get($id);
    public function set($id, $data);
}

$storage->set('foo', 'Hello World!');

echo $storage->get('foo'); // Hello World!
```

You can read all about those interfaces in the [Interfaces documentation](doc/interfaces.md).

## Adapters

Adapters are classes that implement the `Storage` or `MapStorage` interfaces.

Backends store data into a backend:

- `FileStorage` (implements `Storage`)
- `MultipleFileStorage` (implements `MapStorage`)
- `MemoryStorage` (implements `Storage`)
- `ArrayStorage` (implements `MapStorage`)

Transformers wrap another storage to transform the data before storage and after retrieval.

- `JsonEncoder`
- `YamlEncoder`
- `PhpSerializerEncoder`
- `ObjectArrayMapper`
- `AesEncrypter`
- `ArrayMapAdapter`

You can read all about the adapters in the [Adapters documentation](doc/adapters.md).

## Advanced usage

The beauty behind data transformers is that they can be chained:

```php
// Store data in files
$storage = new MultipleFileStorage('some/directory');

// Wrap the storage to encode the data in JSON
$storage = new JsonEncoder($storage);

// Wrap the storage to map objects to array and vice-versa
// (because JSON can't deserialize arrays into PHP objects of a specific class)
$storage = new ObjectArrayMapper($storage, 'MyClass');

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
