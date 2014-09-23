---
currentMenu: home
---

# BlackBox

BlackBox is a storage library that abstracts backends and data transformation behind simple interfaces.

Store data. "Where" and "how" is a decision for later.

## Usage

The `StorageInterface` is very simple to use:

```php
namespace BlackBox;

interface StorageInterface
{
    public function get($id);
    public function set($id, $data);
}

$storage->set('foo', 'Hello World!');

echo $storage->get('foo'); // Hello World!
```

## Adapters

Backends implement the `StorageInterface` and store data into a backend:

- `MultipleFileStorage`
- `ArrayStorage`

Transformers also implement the `StorageInterface`. They are wrapping another storage
to transform the data before storage and after retrieval.

- `JsonEncoder`
- `PhpSerializerEncoder`
- `ObjectArrayMapper`
- `AesEncrypter`

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
