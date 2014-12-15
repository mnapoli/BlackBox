<?php

namespace Tests\BlackBox\Transformer;

use BlackBox\Transformer\AesEncrypter;
use Crypt_AES;

/**
 * @covers \BlackBox\Transformer\AesEncrypter
 */
class AesEncrypterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AesEncrypter
     */
    private $transformer;

    /**
     * @var Crypt_AES
     */
    private $encrypter;

    public function setUp()
    {
        $this->encrypter = new Crypt_AES(CRYPT_AES_MODE_CBC);
        $this->encrypter->setKey('foo');

        $this->transformer = new AesEncrypter($this->encrypter);
    }
    /**
     * @test
     */
    public function it_should_encrypt()
    {
        $data = 'Hello world!';

        $encrypted = $this->transformer->transform($data);

        $this->assertEquals($data, $this->encrypter->decrypt($encrypted));
    }

    /**
     * @test
     */
    public function it_should_not_encrypt_null()
    {
        $encrypted = $this->transformer->transform(null);

        $this->assertNull($encrypted);
    }

    /**
     * @test
     */
    public function it_should_decrypt()
    {
        $data = 'Hello world!';
        $encrypted = $this->encrypter->encrypt($data);

        $this->assertEquals($data, $this->transformer->reverseTransform($encrypted));
    }

    /**
     * @test
     */
    public function it_should_not_decrypt_null()
    {
        $this->assertNull($this->transformer->reverseTransform(null));
    }

    /**
     * @test
     * @expectedException \BlackBox\Exception\StorageException
     * @expectedExceptionMessage The AesEncrypter can only encrypt and decrypt strings, integer given
     */
    public function it_should_fail_with_non_strings()
    {
        $this->transformer->reverseTransform(123);
    }

    /**
     * @test
     * @expectedException \BlackBox\Exception\StorageException
     * @expectedExceptionMessage The AesEncrypter can only encrypt and decrypt strings, stdClass given
     */
    public function it_should_fail_with_objects()
    {
        $this->transformer->reverseTransform(new \stdClass());
    }
}
