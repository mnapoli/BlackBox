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

### `JmsSerializer`

[JMS Serializer library](http://jmsyst.com/libs/serializer) integration to serialize objects to string (JSON, XML or YAML).

Example:

```php
// Create and configure the JMS Serializer
$jmsSerializer = SerializerBuilder::create()->build();

// The JMS serializer could then be used like this:
// $serializer->deserialize($jsonData, 'MyNamespace\MyObject', 'json');

// So we create our transformer like this:
$transformer = new JmsSerializer($jmsSerializer, 'json', 'MyNamespace\MyObject');
```

See the [JMS Serializer](http://jmsyst.com/libs/serializer) documentation to learn how to configure it.
