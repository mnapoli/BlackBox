---
currentMenu: example_encrypted
---

This is an example of encrypted file storage:

```php
$key = 'some-random-and-long-encryption-key';

$storage = new MapWithTransformers(
    new MultipleFileStorage('/tmp')
);
$storage->addTransformer(new PhpSerializeEncoder);
$storage->addTransformer(AesEncrypter::createDefault($key));
```

When storing a PHP variable, it will be:

- serialized into a string
- then encrypted with AES
- then written to disk into a file located in `/tmp`

When retrieving data, it will be:

- read from a file located in `/tmp`
- then decrypted
- then unserialized into a PHP variable
