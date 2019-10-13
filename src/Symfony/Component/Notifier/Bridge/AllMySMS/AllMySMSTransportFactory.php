<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Notifier\Bridge\AllMySMS;

use Symfony\Component\Notifier\Exception\UnsupportedSchemeException;
use Symfony\Component\Notifier\Transport\AbstractTransportFactory;
use Symfony\Component\Notifier\Transport\Dsn;
use Symfony\Component\Notifier\Transport\TransportInterface;

/**
 * @author Quentin Dequippe <quentin@dequippe.tech>
 */
final class AllMySMSTransportFactory extends AbstractTransportFactory
{
    public function create(Dsn $dsn): TransportInterface
    {
        $scheme = $dsn->getScheme();
        $login = $this->getUser($dsn);
        $apiKey = $this->getPassword($dsn);
        $tpoa = $dsn->getOption('tpoa');
        $host = 'default' === $dsn->getHost() ? null : $dsn->getHost();
        $port = $dsn->getPort();

        if ('allmysms' === $scheme) {
            return (new AllMySMSTransport($login, $apiKey, $this->client, $tpoa, $this->dispatcher))->setHost($host)->setPort($port);
        }

        throw new UnsupportedSchemeException($dsn, 'allmysms', $this->getSupportedSchemes());
    }

    protected function getSupportedSchemes(): array
    {
        return ['allmysms'];
    }
}
