<?php

namespace Symfony\Test\Component\HttpFoundation\SessionStorage;

use Symfony\Component\HttpFoundation\SessionStorage\MockFileSessionStorage;
use Symfony\Component\HttpFoundation\SessionFlash\FlashBag;
use Symfony\Component\HttpFoundation\SessionAttribute\AttributeBag;

/**
 * Test class for MockFileSessionStorage.
 *
 * @author Drak <drak@zikula.org>
 */
class MockFileSessionStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $sessionDir;

    /**
     * @var FileMockSessionStorage
     */
    protected $storage;

    protected function setUp()
    {
        $this->sessionDir = sys_get_temp_dir().'/sf2test';
        $this->storage = $this->getStorage();
    }

    protected function tearDown()
    {
        $this->sessionDir = null;
        $this->storage = null;
        array_map('unlink', glob($this->sessionDir.'/*.session'));
        if (is_dir($this->sessionDir)) {
            rmdir($this->sessionDir);
        }
    }

    public function testStart()
    {
        $this->assertEquals('', $this->storage->getId());
        $this->assertTrue($this->storage->start());
        $id = $this->storage->getId();
        $this->assertNotEquals('', $this->storage->getId());
        $this->assertTrue($this->storage->start());
        $this->assertEquals($id, $this->storage->getId());
    }

    public function testRegenerate()
    {
        $this->storage->start();
        $this->storage->getBag('attributes')->set('regenerate', 1234);
        $this->storage->regenerate();
        $this->assertEquals(1234, $this->storage->getBag('attributes')->get('regenerate'));
        $this->storage->regenerate(true);
        $this->assertEquals(1234, $this->storage->getBag('attributes')->get('regenerate'));
    }

    public function testGetId()
    {
        $this->assertEquals('', $this->storage->getId());
        $this->storage->start();
        $this->assertNotEquals('', $this->storage->getId());
    }

    public function testSave()
    {
        $this->storage->start();
        $this->assertNotEquals('108', $this->storage->getBag('attributes')->get('new'));
        $this->assertFalse($this->storage->getBag('flashes')->has('newkey'));
        $this->storage->getBag('attributes')->set('new', '108');
        $this->storage->getBag('flashes')->set('newkey', 'test');
        $this->storage->save();

        $storage = $this->getStorage();
        $storage->start();
        $this->assertEquals('108', $storage->getBag('attributes')->get('new'));
        $this->assertTrue($storage->getBag('flashes')->has('newkey'));
        $this->assertEquals('test', $storage->getBag('flashes')->get('newkey'));
    }

    public function testMultipleInstances()
    {
        $storage1 = $this->getStorage();
        $storage1->start();
        $storage1->getBag('attributes')->set('foo', 'bar');
        $storage1->save();

        $storage2 = $this->getStorage();
        $storage2->start();
        $this->assertEquals('bar', $storage2->getBag('attributes')->get('foo'), 'values persist between instances');
    }

    private function getStorage(array $options = array())
    {
        $storage = new MockFileSessionStorage($this->sessionDir, $options);
        $storage->registerBag(new FlashBag);
        $storage->registerBag(new AttributeBag);

        return $storage;
    }
}
