---
currentMenu: backends
---

Backends are classes that implement the `Storage` or `MapStorage` interfaces.

## Backends

### `FileStorage`

*Implements `Storage`.*

Stores data in a single file.

```php
$storage = new FileStorage('some/file.txt');
$storage->setData('Hello world!');
```

### `MultipleFileStorage`

*Implements `MapStorage`.*

Stores data in multiple files (one file per ID).

```php
$storage = new MultipleFileStorage('some/writable/directory', $extension = 'txt');
```

File names are constructed from the ids. If `$extension` is provided, then it is used as file extension.

### `MemoryStorage`

*Implements `Storage`.*

Stores data in memory. Obviously the data is not persistent between requests.
This backend can be useful for tests or quick prototyping.

### `ArrayStorage`

*Implements `MapStorage`.*

Stores data in an array in memory. Obviously the data is not persistent between requests.
This backend can be useful for tests or quick prototyping.

## Transformers

### `JsonEncoder`

Encodes data from and to JSON.

```php
// Wrap another storage with JSON encoding
$storage = new JsonEncoder($otherStorage, $pretty);
```

If `$pretty` is true, then the JSON will be formatted to be human readable (false by default).

### `YamlEncoder`

Encodes data from and to YAML.

```php
$storage = new YamlEncoder($otherStorage);
```

To use this transformer, you will need to install the `Symfony\YAML` component:

```json
{
    "require": {
        "symfony/yaml": "~2.1"
    }
}
```

### `PhpSerializerEncoder`

Encodes data using the PHP `serialize` function.

```php
$storage = new PhpSerializerEncoder($otherStorage);
```

### `ObjectArrayMapper`

Maps objects to arrays and vice-versa.

```php
$storage = new ObjectArrayMapper($otherStorage, 'MyClass');

class MyClass {
    private $foo = 'Hello';
}

// Will be serialized to
array(
    'foo' => 'Hello'
);
```

On storage, the mapper will extract all the object's properties (including protected and privates) and put them into
an array.

On retrieval, it will create a new instance of the class (without calling the constructor) and restore the properties
values from the array.

### `AesEncrypter`

Encrypts and decrypts data using AES encryption.

```php
$encrypter = new Crypt_AES(CRYPT_AES_MODE_CBC);
$encrypter->setKey($encryptionKey);

$storage = new AesEncrypter($otherStorage, $encrypter);

// Same as above:
$storage = AesEncrypter::createDefault($otherStorage, $encryptionKey);
```

Remember to store the encryption key securely!

To use this transformer, you will need to install the `phpseclib`:

```json
{
    "require": {
        "phpseclib/phpseclib": "*"
    }
}
```

### `ArrayMapAdapter`

This adapter transforms a `Storage` into a `MapStorage`. To do this, it stores the "map" into a PHP array.

For example, if you want to store a Map into a `FileStorage` (single file):

```php
// This storage doesn't implement `MapStorage`
$storage = new FileStorage($file);

// You can now use the `MapStorage` interface
$storage = new ArrayMapAdapter(
    new JsonEncoder($storage)
);
```

In this example, we need to use the `JsonEncoder` because it isn't possible to store a PHP array into a file.
So the array will be encoded in JSON before being written to disk.

Have a look at the [](examples/json-single-file.md)
