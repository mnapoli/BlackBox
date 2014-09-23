---
currentMenu: symfony
---

# BlackBox with [Symfony](http://symfony.com/)

If you are using the [Symfony framework](http://symfony.com/), here is an example of configuration for a
file storage with JSON encoding:

```yaml
services:

    my_storage:
        class: BlackBox\Transformer\JsonEncoder
        arguments: [ "@my_storage.backend" ]

    my_storage.backend:
        class: BlackBox\Adapter\MultipleFileStorage
        arguments: [ "%kernel.root_dir%/data", "json" ]

```
