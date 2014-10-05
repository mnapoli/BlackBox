<?php

namespace BlackBox\Transformer;

use BlackBox\MapStorage;
use BlackBox\Storage;

/**
 * Maps objects to array and vice-versa.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class ObjectArrayMapper extends AbstractTransformer implements MapStorage
{
    /**
     * @var string
     */
    private $class;

    /**
     * {@inheritdoc}
     * @param string $class The class to which array should be mapped.
     */
    public function __construct(Storage $wrapped, $class)
    {
        parent::__construct($wrapped);
        $this->class = (string) $class;
    }

    /**
     * {@inheritdoc}
     */
    protected function transform($data)
    {
        if (is_object($data)) {
            $data = $this->objectToArray($data);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    protected function reverseTransform($data)
    {
        if (is_array($data)) {
            $data = $this->arrayToObject($data, $this->class);
        }

        return $data;
    }

    /**
     * Turn an object into an array.
     *
     * @param object $object
     *
     * @return array
     */
    private function objectToArray($object)
    {
        $class = new \ReflectionClass($object);

        $array = [];

        foreach ($class->getProperties() as $property) {
            if ($property->isStatic()) {
                continue;
            }

            if (! $property->isPublic()) {
                $property->setAccessible(true);
            }

            $array[$property->getName()] = $property->getValue($object);
        }

        return $array;
    }

    /**
     * Turn an array into an object.
     *
     * @param array  $array
     * @param string $targetClass
     *
     * @return object
     */
    private function arrayToObject(array $array, $targetClass)
    {
        $class = new \ReflectionClass($targetClass);

        $object = $class->newInstanceWithoutConstructor();

        foreach ($class->getProperties() as $property) {
            if (! array_key_exists($property->getName(), $array) || $property->isStatic()) {
                continue;
            }

            if (! $property->isPublic()) {
                $property->setAccessible(true);
            }

            $property->setValue($object, $array[$property->getName()]);
        }

        return $object;
    }
}
