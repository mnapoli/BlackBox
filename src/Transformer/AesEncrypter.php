<?php

namespace BlackBox\Transformer;

use BlackBox\Exception\StorageException;
use BlackBox\StorageInterface;
use Crypt_AES;

/**
 * Encrypts and decrypts data using AES.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class AesEncrypter implements StorageInterface
{
    /**
     * @var StorageInterface
     */
    private $wrapped;

    /**
     * @var Crypt_AES
     */
    private $encrypter;

    /**
     * Create an instance using the default parameters for the encrypter.
     *
     * @param StorageInterface $wrapped
     * @param string           $encryptionKey The secret encrypt key to use.
     *
     * @return AesEncrypter
     */
    public static function createDefault(StorageInterface $wrapped, $encryptionKey)
    {
        $encrypter = new Crypt_AES(CRYPT_AES_MODE_CBC);
        $encrypter->setKey($encryptionKey);

        return new static($wrapped, $encrypter);
    }

    public function __construct(StorageInterface $wrapped, Crypt_AES $encrypter)
    {
        $this->wrapped = $wrapped;
        $this->encrypter = $encrypter;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $data = $this->wrapped->get($id);

        if ($data === null) {
            return null;
        }

        $this->assertIsString($data);

        return $this->encrypter->decrypt($data);
    }

    /**
     * {@inheritdoc}
     */
    public function set($id, $data)
    {
        if ($data !== null) {
            $this->assertIsString($data);

            $data = $this->encrypter->encrypt($data);
        }

        $this->wrapped->set($id, $data);
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
