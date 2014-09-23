---
currentMenu: php-di
---

# BlackBox with [PHP-DI](http://php-di.org/)

If you are using [PHP-DI](http://php-di.org/) as a dependency injection container, here is an example a
file storage with JSON encoding:

```php
<?php

use ...;

return [

    StorageInterface::class => DI\object(JsonEncoder::class)
        ->constructor(DI\link('storage.file')),

    'storage.file' => DI\object(MultipleFileStorage::class)
        ->constructor('some/directory', 'json'),

];
```

If the configuration gets too complex and you'd rather write PHP code, don't forget that you can also use a closure:

```php
<?php

use ...;

return [

    StorageInterface::class => DI\factory(function () {
        $backend = new MultipleFileStorage('some/directory', 'json');
        return new JsonEncoder($backend);
    }),

];
```
