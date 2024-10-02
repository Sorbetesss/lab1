<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Notifier\Bridge\Sweego\Tests;

use Symfony\Component\Notifier\Bridge\Sweego\SweegoTransportFactory;
use Symfony\Component\Notifier\Test\TransportFactoryTestCase;

final class SweegoTransportFactoryTest extends TransportFactoryTestCase
{
    public function createFactory(): SweegoTransportFactory
    {
        return new SweegoTransportFactory();
    }

    public static function createProvider(): iterable
    {
        yield [
            'sweego://host.test',
            'sweego://apiKey@host.test',
        ];
    }

    public static function supportsProvider(): iterable
    {
        yield [true, 'sweego://apiKey@default'];
        yield [false, 'somethingElse://apiKey@default'];
    }

    public static function unsupportedSchemeProvider(): iterable
    {
        yield ['somethingElse://apiKey@default'];
    }
}
