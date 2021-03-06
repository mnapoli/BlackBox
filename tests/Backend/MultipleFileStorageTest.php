<?php

namespace Tests\BlackBox\Transformer;

use BlackBox\Backend\DirectoryStorage;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * @covers \BlackBox\Backend\DirectoryStorage
 */
class MultipleFileStorageTest extends \PHPUnit_Framework_TestCase
{
    private $directory;

    protected function setUp()
    {
        parent::setUp();
        $this->directory = __DIR__ . '/../tmp';
        $this->clearDirectory($this->directory);
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->clearDirectory($this->directory);
    }

    /**
     * @test
     * @dataProvider extensionProvider
     */
    public function it_should_store_data_as_map($extension)
    {
        $storage = new DirectoryStorage($this->directory, $extension);

        $storage->set('foo', 'bar');

        $this->assertDataStoredInFile('bar', 'foo', $extension);
        $this->assertEquals('bar', $storage->get('foo'));
    }

    /**
     * @test
     * @dataProvider extensionProvider
     */
    public function it_should_be_traversable($extension)
    {
        $storage = new DirectoryStorage($this->directory, $extension);

        $this->assertEquals([], iterator_to_array($storage));

        $storage->set('foo', 'bar');

        $this->assertEquals([ 'foo' => 'bar' ], iterator_to_array($storage));
    }

    public function extensionProvider()
    {
        return [
            'no extension' => [null],
            'txt' => ['txt'],
            '.txt' => ['.txt'],
        ];
    }

    /**
     * @test
     */
    public function it_should_handle_non_existing_files()
    {
        $storage = new DirectoryStorage($this->directory);

        $this->assertNull($storage->get('foo'));
    }

    /**
     * @test
     */
    public function it_should_handle_special_characters()
    {
        $storage = new DirectoryStorage($this->directory);

        $filename = '../foo-bar.hello\world!';

        $storage->set($filename, 'Test');

        $this->assertDataStoredInFile('Test', '%2E%2E%2Ffoo%2Dbar%2Ehello%5Cworld%21', '');
        $this->assertEquals('Test', $storage->get($filename));
    }

    private function clearDirectory($directory)
    {
        $fs = new Filesystem();
        $fs->remove((new Finder())->in($directory)->exclude('.gitignore'));
    }

    private function assertDataStoredInFile($expectedData, $id, $extension)
    {
        $extension = ltrim($extension, '.');

        $filename = $this->directory . '/' . $id;
        $filename = $extension ? $filename . '.' . $extension : $filename;

        $this->assertFileExists($filename);
        $this->assertEquals($expectedData, file_get_contents($filename));
    }
}
