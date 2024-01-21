<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Lock\Store;

use Doctrine\DBAL\Connection;
use Relay\Relay;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Lock\Exception\InvalidArgumentException;
use Symfony\Component\Lock\PersistingStoreInterface;

/**
 * StoreFactory create stores and connections.
 *
 * @author Jérémy Derussé <jeremy@derusse.com>
 */
class StoreFactory
{
    public static function createStore(#[\SensitiveParameter] object|string $connection): PersistingStoreInterface
    {
        switch (true) {
            case $connection instanceof \Redis:
            case $connection instanceof Relay:
            case $connection instanceof \RedisArray:
            case $connection instanceof \RedisCluster:
            case $connection instanceof \Predis\ClientInterface:
                return new RedisStore($connection);

            case $connection instanceof \Memcached:
                return new MemcachedStore($connection);

            case $connection instanceof \MongoDB\Collection:
                return new MongoDbStore($connection);

            case $connection instanceof \PDO:
                return new PdoStore($connection);

            case $connection instanceof Connection:
                return new DoctrineDbalStore($connection);

            case $connection instanceof \Zookeeper:
                return new ZookeeperStore($connection);

            case !\is_string($connection):
                throw new InvalidArgumentException(sprintf('Unsupported Connection: "%s".', get_debug_type($connection)));
            case 'flock' === $connection:
                return new FlockStore();

            case str_starts_with($connection, 'flock://'):
                return new FlockStore(substr($connection, 8));

            case 'semaphore' === $connection:
                return new SemaphoreStore();

            case preg_match('~^(rediss?|memcached):~', $connection):
                if (!class_exists(AbstractAdapter::class)) {
                    throw new InvalidArgumentException('Unsupported Redis or Memcached DSN. Try running "composer require symfony/cache".');
                }
                $storeClass = str_starts_with($connection, 'memcached:') ? MemcachedStore::class : RedisStore::class;
                $connection = AbstractAdapter::createConnection($connection, ['lazy' => true]);

                return new $storeClass($connection);

            case str_starts_with($connection, 'mongodb'):
                return new MongoDbStore($connection);

            case preg_match('~^(mssql|mysql2?|oci8|pdo_oci|pgsql|postgres|postgresql|sqlite3?)://~', $connection):
                return new DoctrineDbalStore($connection);

            case preg_match('~^(mysql|oci|pgsql|sqlsrv|sqlite):~', $connection):
                return new PdoStore($connection);

            case preg_match('~^(pgsql|postgres|postgresql)\+advisory://~', $connection):
                return new DoctrineDbalPostgreSqlStore($connection);

            case str_starts_with($connection, 'pgsql+advisory:'):
                return new PostgreSqlStore(preg_replace('/^([^:+]+)\+advisory/', '$1', $connection));

            case str_starts_with($connection, 'zookeeper://'):
                return new ZookeeperStore(ZookeeperStore::createConnection($connection));

            case 'in-memory' === $connection:
                return new InMemoryStore();
        }

        throw new InvalidArgumentException(sprintf('Unsupported Connection: "%s".', $connection));
    }
}
