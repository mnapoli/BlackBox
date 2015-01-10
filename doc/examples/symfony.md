---
currentMenu: symfony
---

# BlackBox with [Symfony](http://symfony.com/)

If you are using the [Symfony framework](http://symfony.com/), here is an example of configuration for a
file storage with JSON encoding:

```yaml
services:

    storage:
        class: BlackBox\Adapter\StorageWithTransformers
        arguments: [ "@file_storage" ]
        calls:
            - [ addTransformer, [ @json_encoder ] ]

    file_storage:
        class: BlackBox\Backend\FileStorage
        arguments: [ "%kernel.root_dir%/data/file.json" ]

    json_encoder:
        class: BlackBox\Transformer\JsonEncoder

```
