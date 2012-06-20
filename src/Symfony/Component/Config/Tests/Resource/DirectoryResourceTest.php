<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Config\Tests\Resource;

use Symfony\Component\Config\Resource\DirectoryResource;

class DirectoryResourceTest extends \PHPUnit_Framework_TestCase
{
    protected $directory;

    protected function setUp()
    {
        $this->directory = sys_get_temp_dir().'/symfonyDirectoryIterator';
        if (!file_exists($this->directory)) {
            mkdir($this->directory);
        }
        touch($this->directory.'/tmp.xml');
    }

    protected function tearDown()
    {
        if (!is_dir($this->directory)) {
            return;
        }
        $this->removeDirectory($this->directory);
    }

    protected function removeDirectory($directory)
    {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory), \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($iterator as $path) {
            if (preg_match('#/\.\.?$#', $path->__toString())) {
                continue;
            }
            if ($path->isDir()) {
               rmdir($path->__toString());
            } else {
               unlink($path->__toString());
            }
        }
        rmdir($directory);
    }

    /**
     * @covers Symfony\Component\Config\Resource\DirectoryResource::getId
     */
    public function testGetId()
    {
        $resource1 = new DirectoryResource($this->directory);
        $resource2 = new DirectoryResource($this->directory);
        $resource3 = new DirectoryResource($this->directory, '/\.(foo|xml)$/');

        $this->assertNotNull($resource1->getId());
        $this->assertEquals($resource1->getId(), $resource2->getId());
        $this->assertNotEquals($resource1->getId(), $resource3->getId());
    }

    /**
     * @covers Symfony\Component\Config\Resource\DirectoryResource::getResource
     */
    public function testGetResource()
    {
        $resource = new DirectoryResource($this->directory);
        $this->assertEquals($this->directory, $resource->getResource(), '->getResource() returns the path to the resource');
    }

    public function testGetPattern()
    {
        $resource = new DirectoryResource('foo', 'bar');
        $this->assertEquals('bar', $resource->getPattern());
    }

    /**
     * @covers Symfony\Component\Config\Resource\DirectoryResource::isFresh
     */
    public function testIsFresh()
    {
        $resource = new DirectoryResource($this->directory);
        $this->assertTrue($resource->isFresh(time() + 10), '->isFresh() returns true if the resource has not changed');
        $this->assertFalse($resource->isFresh(time() - 86400), '->isFresh() returns false if the resource has been updated');

        $resource = new DirectoryResource('/____foo/foobar'.rand(1, 999999));
        $this->assertFalse($resource->isFresh(time()), '->isFresh() returns false if the resource does not exist');
    }

    /**
     * @covers Symfony\Component\Config\Resource\DirectoryResource::isFresh
     */
    public function testIsFreshUpdateFile()
    {
        $resource = new DirectoryResource($this->directory);
        touch($this->directory.'/tmp.xml', time() + 20);
        $this->assertFalse($resource->isFresh(time() + 10), '->isFresh() returns false if an existing file is modified');
    }

    /**
     * @covers Symfony\Component\Config\Resource\DirectoryResource::isFresh
     */
    public function testIsFreshNewFile()
    {
        $resource = new DirectoryResource($this->directory);
        touch($this->directory.'/new.xml', time() + 20);
        $this->assertFalse($resource->isFresh(time() + 10), '->isFresh() returns false if a new file is added');
    }

    /**
     * @covers Symfony\Component\Config\Resource\DirectoryResource::isFresh
     */
    public function testIsFreshDeleteFile()
    {
        $resource = new DirectoryResource($this->directory);
        unlink($this->directory.'/tmp.xml');
        $this->assertFalse($resource->isFresh(time()), '->isFresh() returns false if an existing file is removed');
    }

    /**
     * @covers Symfony\Component\Config\Resource\DirectoryResource::isFresh
     */
    public function testIsFreshDeleteDirectory()
    {
        $resource = new DirectoryResource($this->directory);
        $this->removeDirectory($this->directory);
        $this->assertFalse($resource->isFresh(time()), '->isFresh() returns false if the whole resource is removed');
    }

    /**
     * @covers Symfony\Component\Config\Resource\DirectoryResource::isFresh
     */
    public function testIsFreshCreateFileInSubdirectory()
    {
        $subdirectory = $this->directory.'/subdirectory';
        mkdir($subdirectory);

        $resource = new DirectoryResource($this->directory);
        $this->assertTrue($resource->isFresh(time() + 10), '->isFresh() returns true if an unmodified subdirectory exists');

        touch($subdirectory.'/newfile.xml', time() + 20);
        $this->assertFalse($resource->isFresh(time() + 10), '->isFresh() returns false if a new file in a subdirectory is added');
    }

    /**
     * @covers Symfony\Component\Config\Resource\DirectoryResource::isFresh
     */
    public function testIsFreshModifySubdirectory()
    {
        $resource = new DirectoryResource($this->directory);

        $subdirectory = $this->directory.'/subdirectory';
        mkdir($subdirectory);
        touch($subdirectory, time() + 20);

        $this->assertFalse($resource->isFresh(time() + 10), '->isFresh() returns false if a subdirectory is modified (e.g. a file gets deleted)');
    }

    /**
     * @covers Symfony\Component\Config\Resource\DirectoryResource::isFresh
     */
    public function testFilterRegexListNoMatch()
    {
        $resource = new DirectoryResource($this->directory, '/\.(foo|xml)$/');

        touch($this->directory.'/new.bar', time() + 20);
        $this->assertTrue($resource->isFresh(time() + 10), '->isFresh() returns true if a new file not matching the filter regex is created');
    }

    /**
     * @covers Symfony\Component\Config\Resource\DirectoryResource::isFresh
     */
    public function testFilterRegexListMatch()
    {
        $resource = new DirectoryResource($this->directory, '/\.(foo|xml)$/');

        touch($this->directory.'/new.xml', time() + 20);
        $this->assertFalse($resource->isFresh(time() + 10), '->isFresh() returns false if an new file matching the filter regex is created ');
    }

    /**
     * @covers Symfony\Component\Config\Resource\DirectoryResource::hasFile
     */
    public function testHasFile()
    {
        $resource = new DirectoryResource($this->directory, '/\.foo$/');

        touch($this->directory.'/new.foo', time() + 20);

        $this->assertFalse($resource->hasFile($this->directory.'/tmp.xml'));
        $this->assertTrue($resource->hasFile($this->directory.'/new.foo'));
    }

    /**
     * @covers Symfony\Component\Config\Resource\DirectoryResource::getFilteredChilds
     */
    public function testGetFilteredChilds()
    {
        $resource = new DirectoryResource($this->directory, '/\.(foo|xml)$/');

        touch($file1 = $this->directory.'/new.xml', time() + 20);
        touch($file2 = $this->directory.'/old.foo', time() + 20);
        touch($this->directory.'/old', time() + 20);
        mkdir($dir = $this->directory.'/sub');
        touch($file3 = $this->directory.'/sub/file.foo', time() + 20);

        $childs = $resource->getFilteredChilds();
        $this->assertSame(5, count($childs));

        $childs = array_map(function($item) {
            return (string) $item;
        }, $childs);

        $this->assertContains($file1, $childs);
        $this->assertContains($file2, $childs);
        $this->assertContains($dir, $childs);
        $this->assertContains($this->directory.'/tmp.xml', $childs);
        $this->assertContains($file3, $childs);
    }

    /**
     * @covers Symfony\Component\Config\Resource\DirectoryResource::getFilteredResources
     */
    public function testGetFilteredResources()
    {
        $resource = new DirectoryResource($this->directory, '/\.(foo|xml)$/');

        touch($file1 = $this->directory.'/new.xml', time() + 20);
        touch($file2 = $this->directory.'/old.foo', time() + 20);
        touch($this->directory.'/old', time() + 20);
        mkdir($dir = $this->directory.'/sub');
        touch($file3 = $this->directory.'/sub/file.foo', time() + 20);

        $resources = $resource->getFilteredResources();
        $this->assertSame(4, count($resources));

        $childs = array_map(function($item) {
            return realpath($item->getResource());
        }, $resources);

        $this->assertContains(realpath($file1), $childs);
        $this->assertContains(realpath($file2), $childs);
        $this->assertContains(realpath($dir), $childs);
        $this->assertContains(realpath($this->directory.'/tmp.xml'), $childs);
    }

    /**
     * @covers Symfony\Component\Config\Resource\DirectoryResource::exists
     */
    public function testDirectoryExists()
    {
        $resource = new DirectoryResource($this->directory);

        $this->assertTrue($resource->exists(), '->exists() returns true if directory exists ');

        unlink($this->directory.'/tmp.xml');
        rmdir($this->directory);

        $this->assertFalse($resource->exists(), '->exists() returns false if directory does not exists');
    }

    /**
     * @covers Symfony\Component\Config\Resource\DirectoryResource::getModificationTime
     */
    public function testGetModificationTime()
    {
        $resource = new DirectoryResource($this->directory, '/\.(foo|xml)$/');

        touch($this->directory.'/new.xml', $time = time() + 20);
        $this->assertSame($time, $resource->getModificationTime(), '->getModificationTime() returns time of the last modificated resource');

        touch($this->directory.'/some', time() + 60);
        $this->assertSame($time, $resource->getModificationTime(), '->getModificationTime() returns time of last modificated resource, that only matches pattern');

        touch($this->directory, $time2 = time() + 90);
        $this->assertSame($time2, $resource->getModificationTime(), '->getModificationTime() returns modification time of the directory itself');
    }
}
