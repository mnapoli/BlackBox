<?php

namespace BlackBox\Transformer;

use BlackBox\Exception\StorageException;
use BlackBox\StorageInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Encodes and decodes data into YAML.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class YamlEncoder implements StorageInterface
{
    /**
     * @var StorageInterface
     */
    private $wrapped;

    /**
     * @param StorageInterface $wrapped Wrapped storage.
     */
    public function __construct(StorageInterface $wrapped)
    {
        $this->wrapped = $wrapped;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $data = $this->wrapped->get($id);

        return Yaml::parse($data);
    }

    /**
     * {@inheritdoc}
     */
    public function set($id, $data)
    {
        $this->assertIsArray($data);

        $data = Yaml::dump($data);

        $this->wrapped->set($id, $data);
    }

    private function assertIsArray($data)
    {
        if (! is_array($data)) {
            throw new StorageException(sprintf(
                'The YamlEncoder can only encode arrays, %s given',
                is_object($data) ? get_class($data) : gettype($data)
            ));
        }
    }
}
