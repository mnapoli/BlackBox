<?php

namespace Tests\BlackBox\Transformer;

use BlackBox\Adapter\ArrayStorage;
use BlackBox\Transformer\AesEncrypter;
use Crypt_AES;

/**
 * @covers \BlackBox\Transformer\AesEncrypter
 */
class AesEncrypterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_encrypt()
    {
        $encrypter = $this->given_an_encrypter();

        $wrapped = new ArrayStorage();
        $storage = new AesEncrypter($wrapped, $encrypter);

        $data = 'Hello world!';

        $storage->set('foo', $data);

        $this->assertEquals($data, $encrypter->decrypt($wrapped->get('foo')));
    }

    /**
     * @test
     */
    public function it_should_not_encrypt_null()
    {
        $wrapped = new ArrayStorage();
        $storage = new AesEncrypter($wrapped, $this->given_an_encrypter());

        $storage->set('foo', null);

        $this->assertNull($wrapped->get('foo'));
    }

    /**
     * @test
     */
    public function it_should_decrypt()
    {
        $encrypter = $this->given_an_encrypter();

        $data = 'Hello world!';

        $wrapped = new ArrayStorage();
        $wrapped['foo'] = $encrypter->encrypt($data);

        $storage = new AesEncrypter($wrapped, $encrypter);

        $this->assertEquals($data, $storage->get('foo'));
    }

    /**
     * @test
     */
    public function it_should_not_decrypt_null()
    {
        $wrapped = new ArrayStorage();
        $wrapped['foo'] = null;
        $storage = new AesEncrypter($wrapped, $this->given_an_encrypter());

        $this->assertNull($storage->get('foo'));
    }

    /**
     * @test
     * @expectedException \BlackBox\Exception\StorageException
     * @expectedExceptionMessage The AesEncrypter can only encrypt and decrypt strings, integer given
     */
    public function it_should_fail_with_non_strings()
    {
        $wrapped = new ArrayStorage();
        $storage = new AesEncrypter($wrapped, $this->given_an_encrypter());

        $storage->set('foo', 123);
    }

    /**
     * @test
     * @expectedException \BlackBox\Exception\StorageException
     * @expectedExceptionMessage The AesEncrypter can only encrypt and decrypt strings, stdClass given
     */
    public function it_should_fail_with_objects()
    {
        $wrapped = new ArrayStorage();
        $storage = new AesEncrypter($wrapped, $this->given_an_encrypter());

        $storage->set('foo', new \stdClass());
    }

    private function given_an_encrypter()
    {
        $encrypter = new Crypt_AES(CRYPT_AES_MODE_CBC);
        $encrypter->setKey('foo');

        return $encrypter;
    }
}
