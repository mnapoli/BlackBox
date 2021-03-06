---
currentMenu: example_json_single_file
---

This is an example of storing data in a single file, encoded in JSON.

## Storing a single item

```php
$storage = new StorageWithTransformers(
    new FileStorage('/tmp/data.json')
);
$storage->addTransformer(new JsonEncoder);

$storage->setData('Hello World');
```

## Storing several items

`FileStorage` does not implement the `MapStorage` interface, so you cannot use `$storage->get($id)` and `$storage->set($id, $data)`. The reason for this is obvious: a file is not an array structure.

You can use `DirectoryStorage` instead of `FileStorage` to solve that, but that means having several files written on disk (one for each item). **If you really need to have one file, keep reading**.

The solution would be to store an array in the file (which would be encoded in JSON):

```php
$data = [
    'foo' => 'Hello world'
];
$storage->setData($data);

// Later
$data = $storage->getData();
echo $data['foo'];
```

But instead of handling that array yourself, you can use the **`MapAdapter`** that does this for you. Thanks to that adapter, you can use the `MapStorage` API:

```php
$fileStorage = new StorageWithTransformers(
    new FileStorage('/tmp/data.json')
);
$fileStorage->addTransformer(new JsonEncoder);
$storage = new MapAdapter($fileStorage);

$storage->set('foo', 'Hello World');
```
