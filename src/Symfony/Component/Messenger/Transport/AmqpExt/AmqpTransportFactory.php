<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Messenger\Transport\AmqpExt;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Transport\Serialization\DecoderInterface;
use Symfony\Component\Messenger\Transport\Serialization\EncoderInterface;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

/**
 * @author Samuel Roze <samuel.roze@gmail.com>
 */
class AmqpTransportFactory implements TransportFactoryInterface
{
    private $encoder;
    private $decoder;
    private $debug;
    private $logger;

    public function __construct(EncoderInterface $encoder, DecoderInterface $decoder, bool $debug, LoggerInterface $logger = null)
    {
        $this->encoder = $encoder;
        $this->decoder = $decoder;
        $this->debug = $debug;
        $this->logger = $logger;
    }

    public function createTransport(string $dsn, array $options): TransportInterface
    {
        return new AmqpTransport($this->encoder, $this->decoder, Connection::fromDsn($dsn, $options, $this->debug), $this->logger);
    }

    public function supports(string $dsn, array $options): bool
    {
        return 0 === strpos($dsn, 'amqp://');
    }
}
