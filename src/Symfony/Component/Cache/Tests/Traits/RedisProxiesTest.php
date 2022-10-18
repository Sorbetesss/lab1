<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Cache\Tests\Traits;

use PHPUnit\Framework\TestCase;
use Symfony\Component\VarExporter\LazyProxyTrait;
use Symfony\Component\VarExporter\ProxyHelper;

/**
 * @requires extension redis
 */
class RedisProxiesTest extends TestCase
{
    /**
     * @testWith ["Redis"]
     *           ["RedisCluster"]
     */
    public function testRedis5Proxy($class)
    {
        $proxy = file_get_contents(\dirname(__DIR__, 2)."/Traits/{$class}5Proxy.php");
        $proxy = substr($proxy, 0, 8 + strpos($proxy, "\n    ];"));
        $methods = [];

        foreach ((new \ReflectionClass($class))->getMethods() as $method) {
            if ('reset' === $method->name || method_exists(LazyProxyTrait::class, $method->name)) {
                continue;
            }
            $return = $method->getReturnType() instanceof \ReflectionNamedType && 'void' === (string) $method->getReturnType() ? '' : 'return ';
            $methods[] = "\n    ".ProxyHelper::exportSignature($method, false)."\n".<<<EOPHP
                {
                    {$return}\$this->lazyObjectReal->{$method->name}(...\\func_get_args());
                }

            EOPHP;
        }

        uksort($methods, 'strnatcmp');
        $proxy .= implode('', $methods)."}\n";

        $this->assertStringEqualsFile(\dirname(__DIR__, 2)."/Traits/{$class}5Proxy.php", $proxy);
    }

    /**
     * @testWith ["Redis", "redis"]
     *           ["RedisCluster", "redis_cluster"]
     */
    public function testRedis6Proxy($class, $stub)
    {
        $stub = file_get_contents("https://raw.githubusercontent.com/phpredis/phpredis/develop/{$stub}.stub.php");
        $stub = preg_replace('/^class /m', 'return; \0', $stub);
        $stub = preg_replace('/^return; class ([a-zA-Z]++)/m', 'interface \1StubInterface', $stub, 1);
        eval(substr($stub, 5));

        $proxy = file_get_contents(\dirname(__DIR__, 2)."/Traits/{$class}6Proxy.php");
        $proxy = substr($proxy, 0, 8 + strpos($proxy, "\n    ];"));
        $methods = [];

        foreach ((new \ReflectionClass($class.'StubInterface'))->getMethods() as $method) {
            if ('reset' === $method->name || method_exists(LazyProxyTrait::class, $method->name)) {
                continue;
            }
            $return = $method->getReturnType() instanceof \ReflectionNamedType && 'void' === (string) $method->getReturnType() ? '' : 'return ';
            $methods[] = "\n    ".ProxyHelper::exportSignature($method, false)."\n".<<<EOPHP
                {
                    {$return}\$this->lazyObjectReal->{$method->name}(...\\func_get_args());
                }

            EOPHP;
        }

        uksort($methods, 'strnatcmp');
        $proxy .= implode('', $methods)."}\n";

        $this->assertStringEqualsFile(\dirname(__DIR__, 2)."/Traits/{$class}6Proxy.php", $proxy);
    }
}
