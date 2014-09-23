---
currentMenu: example_encrypted
---

This is an example of encrypted file storage:

```php
$encrypter = new Crypt_AES(CRYPT_AES_MODE_CBC);
$encrypter->setKey('some-random-and-long-encryption-key');

$storage = new PhpSerializerEncoder(
    new AesEncrypter(
        new MultipleFileStorage('/tmp'),
        $encrypter
    )
);
```

When storing a PHP variable, it will be:

- serialized into a string
- then encrypted with AES
- then written to disk into a file located in `/tmp`

When retrieving data, it will be:

- read from a file located in `/tmp`
- then decrypted
- then unserialized into a PHP variable
