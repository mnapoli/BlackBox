---
currentMenu: transformers
---

Transformers are classes that transform the data before it is stored or retrieved.

They can be used on top of another storage like so:

```php
$storage = new StorageWithTransformers(
    new FileStorage('data.json')
);
$storage->addTransformer(new JsonEncoder());

$storage->setData('Hello World!');
echo $storage->getData();
```

## `JsonEncoder`

Encodes data from and to JSON.

```php
$storage = new JsonEncoder($pretty);
```

If `$pretty` is true, then the JSON will be formatted to be human readable (false by default).

## `YamlEncoder`

Encodes data from and to YAML.

```php
$storage = new YamlEncoder();
```

To use this transformer, you will need to install the `Symfony\YAML` component:

```
composer require symfony/yaml
```

## `PhpSerializerEncoder`

Encodes data using the PHP `serialize` function.

```php
$storage = new PhpSerializerEncoder();
```

## `ObjectArrayMapper`

Maps objects to arrays and vice-versa.

```php
$storage = new ObjectArrayMapper('MyClass');

class MyClass {
    private $foo = 'Hello';
}

// Will be serialized to
array(
    'foo' => 'Hello'
);
```

On storage, the mapper will extract all the object's properties (including protected and privates) and put them into an array.

On retrieval, it will create a new instance of the class (without calling the constructor) and restore the properties values from the array.

## `AesEncrypter`

Encrypts and decrypts data using AES encryption.

```php
$encrypter = new Crypt_AES(CRYPT_AES_MODE_CBC);
$encrypter->setKey($encryptionKey);

$storage = new AesEncrypter($encrypter);

// Same as above:
$storage = AesEncrypter::createDefault($encryptionKey);
```

Remember to store the encryption key securely!

To use this transformer, you will need to install the `phpseclib`:

```
composer require phpseclib/phpseclib
```

## `JmsSerializer`

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

To use this transformer, you will need to install the JMS Serializer library:

```
composer require jms/serializer
```
