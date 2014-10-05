<?php

namespace BlackBox\Transformer;

use BlackBox\Exception\StorageException;
use BlackBox\MapStorage;
use Symfony\Component\Yaml\Yaml;

/**
 * Encodes and decodes data into YAML.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class YamlEncoder extends AbstractTransformer implements MapStorage
{
    /**
     * {@inheritdoc}
     */
    protected function transform($data)
    {
        $this->assertIsNotObject($data);

        return Yaml::dump($data);
    }

    /**
     * {@inheritdoc}
     */
    protected function reverseTransform($data)
    {
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
