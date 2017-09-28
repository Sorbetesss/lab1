<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Cache\Tests\Simple;

use Symfony\Component\Cache\Simple\RedisCache;
use Symfony\Component\Dsn\Factory\RedisFactory;

class RedisCacheTest extends AbstractRedisCacheTest
{
    public static function setupBeforeClass()
    {
        parent::setupBeforeClass();
        self::$redis = RedisFactory::create('redis://'.getenv('REDIS_HOST'));
    }

    /**
     * @group legacy
     * @expectedDeprecation The %s() method is deprecated since version 3.4 and will be removed in 4.0. Use the RedisFactory::create() method from Dsn component instead.
     */
    public function testCreateConnectionDeprecated()
    {
        RedisCache::createConnection('redis://'.getenv('REDIS_HOST'));
    }

    /**
     * @group legacy
     */
    public function testCreateConnection()
    {
        $redisHost = getenv('REDIS_HOST');

        $redis = RedisCache::createConnection('redis://'.$redisHost);
        $this->assertInstanceOf(\Redis::class, $redis);
        $this->assertTrue($redis->isConnected());
        $this->assertSame(0, $redis->getDbNum());

        $redis = RedisCache::createConnection('redis://'.$redisHost.'/2');
        $this->assertSame(2, $redis->getDbNum());

        $redis = RedisCache::createConnection('redis://'.$redisHost, array('timeout' => 3));
        $this->assertEquals(3, $redis->getTimeout());

        $redis = RedisCache::createConnection('redis://'.$redisHost.'?timeout=4');
        $this->assertEquals(4, $redis->getTimeout());

        $redis = RedisCache::createConnection('redis://'.$redisHost, array('read_timeout' => 5));
        $this->assertEquals(5, $redis->getReadTimeout());
    }

    /**
     * @group legacy
     * @dataProvider provideFailedCreateConnection
     * @expectedException \Symfony\Component\Cache\Exception\InvalidArgumentException
     * @expectedExceptionMessage Redis connection failed
     */
    public function testFailedCreateConnection($dsn)
    {
        RedisCache::createConnection($dsn);
    }

    public function provideFailedCreateConnection()
    {
        return array(
            array('redis://localhost:1234'),
            array('redis://foo@localhost'),
            array('redis://localhost/123'),
        );
    }

    /**
     * @group legacy
     * @dataProvider provideInvalidCreateConnection
     * @expectedException \Symfony\Component\Cache\Exception\InvalidArgumentException
     * @expectedExceptionMessage Invalid Redis DSN
     */
    public function testInvalidCreateConnection($dsn)
    {
        RedisCache::createConnection($dsn);
    }

    public function provideInvalidCreateConnection()
    {
        return array(
            array('foo://localhost'),
            array('redis://'),
        );
    }
}
