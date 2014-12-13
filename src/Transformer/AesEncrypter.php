<?php

namespace BlackBox\Transformer;

use BlackBox\Exception\StorageException;
use BlackBox\Storage;
use Crypt_AES;

/**
 * Encrypts and decrypts data using AES.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class AesEncrypter implements Transformer
{
    /**
     * @var Crypt_AES
     */
    private $encrypter;

    /**
     * Create an instance using the default parameters for the encrypter.
     *
     * @param Storage $wrapped
     * @param string  $encryptionKey The secret encrypt key to use.
     *
     * @return AesEncrypter
     */
    public static function createDefault(Storage $wrapped, $encryptionKey)
    {
        $encrypter = new Crypt_AES(CRYPT_AES_MODE_CBC);
        $encrypter->setKey($encryptionKey);

        return new static($wrapped, $encrypter);
    }

    public function __construct(Crypt_AES $encrypter)
    {
        $this->encrypter = $encrypter;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($data)
    {
        if ($data === null) {
            return null;
        }

        $this->assertIsString($data);

        return $this->encrypter->encrypt($data);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($data)
    {
        if ($data === null) {
            return null;
        }

        $this->assertIsString($data);

        return $this->encrypter->decrypt($data);
    }

    private function assertIsString($data)
    {
        if (! is_string($data)) {
            throw new StorageException(sprintf(
                'The AesEncrypter can only encrypt and decrypt strings, %s given',
                is_object($data) ? get_class($data) : gettype($data)
            ));
        }
    }
}
