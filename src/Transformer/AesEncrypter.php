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
class AesEncrypter extends Transformer
{
    /**
     * @var Crypt_AES
     */
    private $encrypter;

    /**
     * Create an instance using the default parameters for the encrypter.
     *
     * @param string $encryptionKey The secret encrypt key to use.
     */
    public static function createDefault(Storage $storage, string $encryptionKey) : self
    {
        $encrypter = new Crypt_AES(CRYPT_AES_MODE_CBC);
        $encrypter->setKey($encryptionKey);

        return new static($storage, $encrypter);
    }

    public function __construct(Storage $storage, Crypt_AES $encrypter)
    {
        parent::__construct($storage);

        $this->encrypter = $encrypter;
    }

    /**
     * {@inheritdoc}
     */
    protected function transform($data)
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
    protected function restore($data)
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
