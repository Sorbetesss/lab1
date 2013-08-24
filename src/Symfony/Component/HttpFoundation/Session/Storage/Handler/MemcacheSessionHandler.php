<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Session\Storage\Handler;

/**
 * MemcacheSessionHandler.
 *
 * @author Drak <drak@zikula.org>
 *
 * @since v2.1.0
 */
class MemcacheSessionHandler implements \SessionHandlerInterface
{
    /**
     * @var \Memcache Memcache driver.
     */
    private $memcache;

    /**
     * @var integer Time to live in seconds
     */
    private $ttl;

    /**
     * @var string Key prefix for shared environments.
     */
    private $prefix;

    /**
     * Constructor.
     *
     * List of available options:
     *  * prefix: The prefix to use for the memcache keys in order to avoid collision
     *  * expiretime: The time to live in seconds
     *
     * @param \Memcache $memcache A \Memcache instance
     * @param array     $options  An associative array of Memcache options
     *
     * @throws \InvalidArgumentException When unsupported options are passed
     *
     * @since v2.1.0
     */
    public function __construct(\Memcache $memcache, array $options = array())
    {
        if ($diff = array_diff(array_keys($options), array('prefix', 'expiretime'))) {
            throw new \InvalidArgumentException(sprintf(
                'The following options are not supported "%s"', implode(', ', $diff)
            ));
        }

        $this->memcache = $memcache;
        $this->ttl = isset($options['expiretime']) ? (int) $options['expiretime'] : 86400;
        $this->prefix = isset($options['prefix']) ? $options['prefix'] : 'sf2s';
    }

    /**
     * {@inheritDoc}
     *
     * @since v2.1.0
     */
    public function open($savePath, $sessionName)
    {
        return true;
    }

    /**
     * {@inheritDoc}
     *
     * @since v2.1.0
     */
    public function close()
    {
        return $this->memcache->close();
    }

    /**
     * {@inheritDoc}
     *
     * @since v2.1.0
     */
    public function read($sessionId)
    {
        return $this->memcache->get($this->prefix.$sessionId) ?: '';
    }

    /**
     * {@inheritDoc}
     *
     * @since v2.1.0
     */
    public function write($sessionId, $data)
    {
        return $this->memcache->set($this->prefix.$sessionId, $data, 0, time() + $this->ttl);
    }

    /**
     * {@inheritDoc}
     *
     * @since v2.1.0
     */
    public function destroy($sessionId)
    {
        return $this->memcache->delete($this->prefix.$sessionId);
    }

    /**
     * {@inheritDoc}
     *
     * @since v2.1.0
     */
    public function gc($lifetime)
    {
        // not required here because memcache will auto expire the records anyhow.
        return true;
    }

    /**
     * Return a Memcache instance
     *
     * @return \Memcache
     */
    protected function getMemcache()
    {
        return $this->memcache;
    }
}
