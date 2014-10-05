---
currentMenu: interfaces
---

BlackBox defines several interfaces to abstract storages.

## `Storage`

`Storage` is the base interface **that every storage class implements**.

```php
interface Storage
{
    /**
     * Returns the data stored.
     *
     * @throws StorageException Error while retrieving the data.
     *
     * @return mixed Returns null if the storage was empty.
     */
    public function getData();

    /**
     * Stores data in the storage.
     *
     * If the storage contained anything previously, it will be overwritten.
     *
     * @param mixed $data
     *
     * @throws StorageException Error while storing the data.
     *
     * @return void
     */
    public function setData($data);
}
```

## `MapStorage`

The `MapStorage` interface defines storages that can store data as a [map/dictionary](http://en.wikipedia.org/wiki/Associative_array) (just like a PHP associative array).

It can contain multiple items and these items are indexed by an ID.

```php
interface MapStorage extends Storage
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
