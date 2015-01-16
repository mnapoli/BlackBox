---
currentMenu: interfaces
---

BlackBox defines several interfaces to abstract storages.

## `MapStorage`

The `MapStorage` interface defines storages that can store data as a [map/dictionary](http://en.wikipedia.org/wiki/Associative_array) (just like a PHP associative array).

It can contain multiple items and these items are indexed by an ID.

```php
interface MapStorage extends Traversable
{
    /**
     * Returns the data stored under the given ID.
     *
     * @param string $id
     *
     * @throws StorageException Error while retrieving the data.
     *
     * @return mixed
     */
    public function get($id);

    /**
     * Stores data under the given ID.
     *
     * @param string $id
     * @param mixed $data
     *
     * @throws StorageException Error while storing the data.
     *
     * @return void
     */
    public function set($id, $data);
}
```

The interface extends `Traversable`, which means a `MapStorage` can be traversed using foreach:

```php
foreach ($storage as $key => $item) {
    // ...
}
```

If needed to be used with `array_*` functions, you can turn the storage to an array:

```php
$array = iterator_to_array($storage);
```
