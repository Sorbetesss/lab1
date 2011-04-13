<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Tests\Component\Templating\Helper;

use Symfony\Component\Templating\Asset\AssetPackage;
use Symfony\Component\Templating\Helper\AssetsHelper;

class AssetsHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $helper = new AssetsHelper('foo', 'http://www.example.com', 'abcd');
        $this->assertEquals('/foo/', $helper->getBasePath(), '__construct() takes a base path as its first argument');
        $this->assertEquals(new AssetPackage('http://www.example.com', 'abcd'), $helper->getPackage('default'), '->__construct() creates a default asset package');
    }

    public function testGetSetBasePath()
    {
        $helper = new AssetsHelper();
        $helper->setBasePath('foo/');
        $this->assertEquals('/foo/', $helper->getBasePath(), '->setBasePath() prepends a / if needed');
        $helper->setBasePath('/foo');
        $this->assertEquals('/foo/', $helper->getBasePath(), '->setBasePath() appends a / is needed');
        $helper->setBasePath('');
        $this->assertEquals('/', $helper->getBasePath(), '->setBasePath() returns / if no base path is defined');
        $helper->setBasePath('0');
        $this->assertEquals('/0/', $helper->getBasePath(), '->setBasePath() returns /0/ if 0 is given');
    }

    public function testGetVersion()
    {
        $helper = new AssetsHelper(null, array(), 'foo');
        $this->assertEquals('foo', $helper->getVersion(), '->getVersion() returns the version');
    }

    public function testGetUrl()
    {
        $helper = new AssetsHelper();
        $this->assertEquals('http://example.com/foo.js', $helper->getUrl('http://example.com/foo.js'), '->getUrl() does nothing if an absolute URL is given');

        $helper = new AssetsHelper();
        $this->assertEquals('/foo.js', $helper->getUrl('foo.js'), '->getUrl() appends a / on relative paths');
        $this->assertEquals('/foo.js', $helper->getUrl('/foo.js'), '->getUrl() does nothing on absolute paths');

        $helper = new AssetsHelper('/foo');
        $this->assertEquals('/foo/foo.js', $helper->getUrl('foo.js'), '->getUrl() appends the basePath on relative paths');
        $this->assertEquals('/foo.js', $helper->getUrl('/foo.js'), '->getUrl() does not append the basePath on absolute paths');

        $helper = new AssetsHelper(null, 'http://assets.example.com/');
        $this->assertEquals('http://assets.example.com/foo.js', $helper->getUrl('foo.js'), '->getUrl() prepends the base URL');
        $this->assertEquals('http://assets.example.com/foo.js', $helper->getUrl('/foo.js'), '->getUrl() prepends the base URL');

        $helper = new AssetsHelper(null, 'http://www.example.com/foo');
        $this->assertEquals('http://www.example.com/foo/foo.js', $helper->getUrl('foo.js'), '->getUrl() prepends the base URL with a path');
        $this->assertEquals('http://www.example.com/foo/foo.js', $helper->getUrl('/foo.js'), '->getUrl() prepends the base URL with a path');

        $helper = new AssetsHelper('/foo', 'http://www.example.com/');
        $this->assertEquals('http://www.example.com/foo.js', $helper->getUrl('foo.js'), '->getUrl() prepends the base URL and the base path if defined');
        $this->assertEquals('http://www.example.com/foo.js', $helper->getUrl('/foo.js'), '->getUrl() prepends the base URL but not the base path on absolute paths');

        $helper = new AssetsHelper('/bar', 'http://www.example.com/foo');
        $this->assertEquals('http://www.example.com/foo/foo.js', $helper->getUrl('foo.js'), '->getUrl() prepends the base URL and the base path if defined');
        $this->assertEquals('http://www.example.com/foo/foo.js', $helper->getUrl('/foo.js'), '->getUrl() prepends the base URL but not the base path on absolute paths');

        $helper = new AssetsHelper('/bar', 'http://www.example.com/foo', 'abcd');
        $this->assertEquals('http://www.example.com/foo/foo.js?abcd', $helper->getUrl('foo.js'), '->getUrl() appends the version if defined');
    }

    public function testGetUrlLeavesProtocolRelativePathsUntouched()
    {
        $helper = new AssetsHelper(null, 'http://foo.com');
        $this->assertEquals('//bar.com/asset', $helper->getUrl('//bar.com/asset'));
    }

    public function testGetPackageVersion()
    {
        $helper = new AssetsHelper();
        $helper->addPackage('js', new AssetPackage(array(), '1.0.0'));
        $this->assertEquals('1.0.0', $helper->getVersion('js'), '->getVersion() returns a package version');
    }

    public function testGetDefaultVersion()
    {
        $helper = new AssetsHelper(null, null, '1.0.0');
        $this->assertEquals('1.0.0', $helper->getVersion(), '->getVersion() returns the default version');
    }

    /**
     * @dataProvider getVersionFormats
     */
    public function testVersionFormat($format, $expected)
    {
        $helper = new AssetsHelper('/subdir/', null, null, array(
            'default' => new AssetPackage(array(), '1.0.0', $format),
        ));

        $this->assertEquals($expected, $helper->getUrl('images/logo.gif'));
    }

    public function getVersionFormats()
    {
        return array(
            array('release-%2$s/%1$s', '/subdir/release-1.0.0/images/logo.gif'),
            array('%s?%s', '/subdir/images/logo.gif?1.0.0'),
        );
    }

    /**
     * @dataProvider getRootRelativeVersionFormats
     */
    public function testRootRelativeVersionFormat($format, $expected)
    {
        $helper = new AssetsHelper('/subdir/', null, null, array(
            'default' => new AssetPackage(array(), '1.0.0', $format),
        ));

        $this->assertEquals($expected, $helper->getUrl('/images/logo.gif'));
    }

    public function getRootRelativeVersionFormats()
    {
        return array(
            array('release-%2$s/%1$s', '/release-1.0.0/images/logo.gif'),
            array('%s?%s', '/images/logo.gif?1.0.0'),
        );
    }
}
