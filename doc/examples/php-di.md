---
currentMenu: php-di
---

# BlackBox with [PHP-DI](http://php-di.org/)

If you are using [PHP-DI](http://php-di.org/) as a dependency injection container, here is an example
of configuration for a file storage with JSON encoding:

```php
<?php

use ...;

return [

    StorageInterface::class => DI\object(StorageWithTransformers::class)
        ->constructor(DI\link(FileStorage::class))
        ->method('addTransformer', DI\link(JsonEncoder::class)),

    FileStorage::class => DI\object()
        ->constructor('/tmp/file.json'),

];
```

If the configuration gets too complex and you'd rather write PHP code, don't forget that you can also use a closure:

```php
<?php

use ...;

return [

    StorageInterface::class => DI\factory(function () {
        $storage = new StorageWithTransformers(
            new FileStorage('/tmp/data.json')
        );
        $storage->addTransformer(new JsonEncoder);

        return $storage;
    }),

];
```
