<?php

namespace BlackBox\Transformer;

use BlackBox\Exception\StorageException;
use JMS\Serializer\Exception\Exception as JMSException;
use JMS\Serializer\Serializer;

/**
 * Serialize and deserialize data using the JMS Serializer library.
 *
 * @link http://jmsyst.com/libs/serializer
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class JmsSerializer implements Transformer
{
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * The format to pass to the serializer ('json', 'xml' or 'yml').
     *
     * @var string
     */
    private $format;

    /**
     * The class name of objects to deserialize
     *
     * @var string
     */
    private $class;

    /**
     * @param Serializer $serializer JMS serializer instance
     * @param string     $format     The format to pass to the serializer ('json', 'xml' or 'yml')
     * @param string     $class      The class name of objects to deserialize
     */
    public function __construct(Serializer $serializer, $format, $class)
    {
        $this->serializer = $serializer;
        $this->format = (string) $format;
        $this->class = (string) $class;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($data)
    {
        try {
            return $this->serializer->serialize($data, $this->format);
        } catch (JMSException $e) {
            throw new StorageException(
                'The JMS serializer failed serializing the data: ' . $e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($data)
    {
        if ($data === null) {
            return null;
        }

        try {
            return $this->serializer->deserialize($data, $this->class, $this->format);
        } catch (JMSException $e) {
            throw new StorageException(
                'The JMS serializer failed deserializing the data: ' . $e->getMessage(),
                0,
                $e
            );
        }
    }
}
