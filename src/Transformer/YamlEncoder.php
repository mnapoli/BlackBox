<?php

namespace BlackBox\Transformer;

use BlackBox\Exception\StorageException;
use Symfony\Component\Yaml\Yaml;

/**
 * Encodes and decodes data into YAML.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class YamlEncoder implements Transformer
{
    /**
     * {@inheritdoc}
     */
    public function transform($data)
    {
        $this->assertIsNotObject($data);

        return Yaml::dump($data);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($data)
    {
        if ($data === null) {
            return null;
        }

        return Yaml::parse($data);
    }

    private function assertIsNotObject($data)
    {
        if (is_object($data)) {
            throw new StorageException(sprintf(
                'The YamlEncoder cannot encode objects, %s given',
                get_class($data)
            ));
        }
    }
}
