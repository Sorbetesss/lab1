<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Tests\Loader;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\IniFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Loader\DirectoryLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\FileLocator;

class DirectoryLoaderTest extends \PHPUnit_Framework_TestCase
{
    protected static $fixturesPath;

    protected $container;
    protected $loader;

    public static function setUpBeforeClass()
    {
        self::$fixturesPath = realpath(__DIR__.'/../Fixtures/');
    }

    protected function setUp()
    {
        $locator         = new FileLocator(self::$fixturesPath);
        $this->container = new ContainerBuilder();
        $this->loader    = new DirectoryLoader($this->container, $locator);
        $resolver        = new LoaderResolver(array(
            new PhpFileLoader($this->container, $locator),
            new IniFileLoader($this->container, $locator),
            new YamlFileLoader($this->container, $locator),
            $this->loader,
        ));
        $this->loader->setResolver($resolver);
    }

    /**
     * @covers Symfony\Component\DependencyInjection\Loader\DirectoryLoader::__construct
     * @covers Symfony\Component\DependencyInjection\Loader\DirectoryLoader::load
     */
    public function testDirectoryCanBeLoadedRecursively()
    {
        $this->loader->load('directory/');
        $this->assertEquals(array('ini' => 'ini', 'yaml' => 'yaml', 'php' => 'php'), $this->container->getParameterBag()->all(), '->load() takes a single file name as its first argument');
    }

    /**
     * @covers Symfony\Component\DependencyInjection\Loader\DirectoryLoader::__construct
     * @covers Symfony\Component\DependencyInjection\Loader\DirectoryLoader::load
     *
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage The file "foo" does not exist (in:
     */
    public function testExceptionIsRaisedWhenDirectoryDoesNotExist()
    {
        $this->loader->load('foo/');
    }

    /**
     * @covers Symfony\Component\DependencyInjection\Loader\DirectoryLoader::supports
     */
    public function testSupports()
    {
        $loader = new DirectoryLoader(new ContainerBuilder(), new FileLocator());

        $this->assertTrue($loader->supports('foo/'), '->supports() returns true if the resource is loadable');
        $this->assertTrue($loader->supports('foo/', 'directory'), '->supports() returns true if the resource is loadable');
        $this->assertTrue($loader->supports('foo', 'directory'), '->supports() returns true if the resource is loadable');
        $this->assertFalse($loader->supports('foo'), '->supports() returns true if the resource is loadable');
    }
}
