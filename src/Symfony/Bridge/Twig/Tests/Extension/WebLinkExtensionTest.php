<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bridge\Twig\Tests\Extension;

use Fig\Link\Link;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Twig\Extension\WebLinkExtension;
use Symfony\Component\WebLink\WebLinkManager;

/**
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class WebLinkExtensionTest extends TestCase
{
    /**
     * @var WebLinkManager
     */
    private $manager;

    /**
     * @var WebLinkExtension
     */
    private $extension;

    protected function setUp()
    {
        $this->manager = new WebLinkManager();
        $this->extension = new WebLinkExtension($this->manager);
    }

    public function testLink()
    {
        $this->assertEquals('/foo.css', $this->extension->link('/foo.css', 'preload', array('as' => 'style', 'nopush' => true)));

        $link = (new Link('preload', '/foo.css'))->withAttribute('as', 'style')->withAttribute('nopush', true);
        $this->assertEquals(array($link), array_values($this->manager->getLinkProvider()->getLinks()));
    }

    public function testPreload()
    {
        $this->assertEquals('/foo.css', $this->extension->preload('/foo.css', array('as' => 'style', 'crossorigin' => true)));

        $link = (new Link('preload', '/foo.css'))->withAttribute('as', 'style')->withAttribute('crossorigin', true);
        $this->assertEquals(array($link), array_values($this->manager->getLinkProvider()->getLinks()));
    }

    public function testDnsPrefetch()
    {
        $this->assertEquals('/foo.css', $this->extension->dnsPrefetch('/foo.css', array('as' => 'style', 'crossorigin' => true)));

        $link = (new Link('dns-prefetch', '/foo.css'))->withAttribute('as', 'style')->withAttribute('crossorigin', true);
        $this->assertEquals(array($link), array_values($this->manager->getLinkProvider()->getLinks()));
    }

    public function testPreconnect()
    {
        $this->assertEquals('/foo.css', $this->extension->preconnect('/foo.css', array('as' => 'style', 'crossorigin' => true)));

        $link = (new Link('preconnect', '/foo.css'))->withAttribute('as', 'style')->withAttribute('crossorigin', true);
        $this->assertEquals(array($link), array_values($this->manager->getLinkProvider()->getLinks()));
    }

    public function testPrefetch()
    {
        $this->assertEquals('/foo.css', $this->extension->prefetch('/foo.css', array('as' => 'style', 'crossorigin' => true)));

        $link = (new Link('prefetch', '/foo.css'))->withAttribute('as', 'style')->withAttribute('crossorigin', true);
        $this->assertEquals(array($link), array_values($this->manager->getLinkProvider()->getLinks()));
    }

    public function testPrerender()
    {
        $this->assertEquals('/foo.css', $this->extension->prerender('/foo.css', array('as' => 'style', 'crossorigin' => true)));

        $link = (new Link('prerender', '/foo.css'))->withAttribute('as', 'style')->withAttribute('crossorigin', true);
        $this->assertEquals(array($link), array_values($this->manager->getLinkProvider()->getLinks()));
    }

    public function testGetName()
    {
        $this->assertEquals('web_link', (new WebLinkExtension(new WebLinkManager()))->getName());
    }
}
