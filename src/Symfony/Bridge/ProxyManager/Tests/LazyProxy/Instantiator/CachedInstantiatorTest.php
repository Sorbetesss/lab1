<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bridge\ProxyManager\Tests\LazyProxy\Instantiator;

use ProxyManager\Proxy\LazyLoadingInterface;
use Symfony\Bridge\ProxyManager\LazyProxy\Instantiator\CachedInstantiator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Tests for {@see \Symfony\Bridge\ProxyManager\LazyProxy\Instantiator\CachedInstantiator}
 *
 */
class CachedInstantiatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CachedInstantiator
     */
    protected $instantiator;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->instantiator = new CachedInstantiator('.');
    }

    public function testInstantiateProxy()
    {
        $instance     = new \stdClass();
        $container    = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $definition   = new Definition('stdClass');
        $instantiator = function () use ($instance) {
            return $instance;
        };

        /* @var $proxy \ProxyManager\Proxy\LazyLoadingInterface|\ProxyManager\Proxy\ValueHolderInterface */
        $proxy = $this->instantiator->instantiateProxy($container, $definition, 'foo', $instantiator);

        $this->assertInstanceOf('ProxyManager\Proxy\LazyLoadingInterface', $proxy);
        $this->assertInstanceOf('ProxyManager\Proxy\ValueHolderInterface', $proxy);
        $this->assertFalse($proxy->isProxyInitialized());

        $proxy->initializeProxy();

        $this->assertSame($instance, $proxy->getWrappedValueHolderValue());
    }
}
