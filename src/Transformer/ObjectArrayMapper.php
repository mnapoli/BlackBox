<?php

namespace BlackBox\Transformer;

/**
 * Maps objects to array and vice-versa.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class ObjectArrayMapper implements Transformer
{
    /**
     * @var string
     */
    private $class;

    /**
     * @param string $class The class to which array should be mapped.
     */
    public function __construct($class)
    {
        $this->class = (string) $class;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($data)
    {
        if (is_object($data)) {
            $data = $this->objectToArray($data);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($data)
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
