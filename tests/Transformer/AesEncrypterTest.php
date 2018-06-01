<?php

namespace Tests\BlackBox\Transformer;

use BlackBox\Backend\ArrayStorage;
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

    /**
     * @var ArrayStorage
     */
    private $decoratedStorage;

    public function setUp()
    {
        $this->encrypter = new Crypt_AES(CRYPT_AES_MODE_CBC);
        $this->encrypter->setKey('foo');

        $this->decoratedStorage = new ArrayStorage;
        $this->transformer = new AesEncrypter($this->decoratedStorage, $this->encrypter);
    }

    /**
     * @test
     */
    public function it_should_encrypt()
    {
        $data = 'Hello world!';

        $this->transformer->set('foo', $data);
        $encrypted = $this->decoratedStorage->get('foo');

        $this->assertEquals($data, $this->encrypter->decrypt($encrypted));
    }

    /**
     * @test
     */
    public function it_should_not_encrypt_null()
    {
        $this->transformer->set('foo', null);
        $encrypted = $this->decoratedStorage->get('foo');

        $this->assertNull($encrypted);
    }

    /**
     * @test
     */
    public function it_should_decrypt()
    {
        $data = 'Hello world!';
        $this->transformer->set('foo', $data);

        $this->assertEquals($data, $this->transformer->get('foo'));
    }

    /**
     * @test
     */
    public function it_should_not_decrypt_null()
    {
        $this->transformer->set('foo', null);

        $this->assertNull($this->transformer->get(null));
    }

    /**
     * @test
     * @expectedException \BlackBox\Exception\StorageException
     * @expectedExceptionMessage The AesEncrypter can only encrypt and decrypt strings, integer given
     */
    public function it_should_fail_with_non_strings()
    {
        $this->transformer->set('foo', 123);
    }

    /**
     * @test
     * @expectedException \BlackBox\Exception\StorageException
     * @expectedExceptionMessage The AesEncrypter can only encrypt and decrypt strings, stdClass given
     */
    public function it_should_fail_with_objects()
    {
        $this->transformer->set('foo', new \stdClass());
    }

    /**
     * @test
     * @expectedException \BlackBox\Exception\StorageException
     * @expectedExceptionMessage The AesEncrypter can only encrypt and decrypt strings, array given
     */
    public function it_should_fail_with_arrays()
    {
        $this->transformer->set('foo', []);
    }
}
