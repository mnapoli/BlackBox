---
currentMenu: adapters
---

Adapters are implementations of the `StorageInterface`.

They can be of several types:

- **backend**: the implementation for a specific storage backend
- **transformer**: wraps another `StorageInterface` to transform the data before storage and after retrieval

## Backends

### `MultipleFileStorage`

Stores data in multiple files (one file per ID).

```php
$storage = new MultipleFileStorage('some/writable/directory', $extension = 'txt');
```

File names are constructed from the ids. If `$extension` is not null, then it is used as file extension.

### `ArrayStorage`

Stores data in an array in memory. Obviously the data is not persistent between requests.
This backend can be useful for tests or quick prototyping.

The class also implements the `ArrayAccess` interface for easy usage:

```php
$storage = new ArrayStorage();
$storage['foo'] = 'bar';
```

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

To use this adapter, you will need to install the `phpseclib`. Add this to your composer.json:

```json
{
    "require": {
        "phpseclib/phpseclib": "*"
    }
}
```
