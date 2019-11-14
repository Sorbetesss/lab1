<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mailer\Transport;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\LegacyEventDispatcherProxy;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mailer\Exception\LogicException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Message;
use Symfony\Component\Mime\RawMessage;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class AbstractTransport implements TransportInterface
{
    private $dispatcher;
    private $logger;
    private $rate = 0;
    private $lastSent = 0;

    public function __construct(EventDispatcherInterface $dispatcher = null, LoggerInterface $logger = null)
    {
        $this->dispatcher = LegacyEventDispatcherProxy::decorate($dispatcher);
        $this->logger = $logger ?: new NullLogger();
    }

    /**
     * Sets the maximum number of messages to send per second (0 to disable).
     */
    public function setMaxPerSecond(float $rate): self
    {
        if (0 >= $rate) {
            $rate = 0;
        }

        $this->rate = $rate;
        $this->lastSent = 0;

        return $this;
    }

    public function send(RawMessage $message, Envelope $envelope = null): ?SentMessage
    {
        $message = clone $message;

        if (null === $envelope && !$message instanceof Message) {
            throw new LogicException(sprintf('Cannot send a "%s" instance without an explicit Envelope.', \get_class($message)));
        }

        $envelope = null !== $envelope ? clone $envelope : Envelope::create($message);

        if (null !== $this->dispatcher) {
            $event = new MessageEvent($message, $envelope, (string) $this);
            $this->dispatcher->dispatch($event);
            $envelope = $event->getEnvelope();
        }

        if (!$envelope->getRecipients()) {
            return null;
        }

        $message = new SentMessage($message, $envelope);
        $this->doSend($message);

        $this->checkThrottling();

        return $message;
    }

    abstract protected function doSend(SentMessage $message): void;

    /**
     * @param Address[] $addresses
     *
     * @return string[]
     */
    protected function stringifyAddresses(array $addresses): array
    {
        return array_map(function (Address $a) {
            return $a->toString();
        }, $addresses);
    }

    protected function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    private function checkThrottling()
    {
        if (0 == $this->rate) {
            return;
        }

        $sleep = (1 / $this->rate) - (microtime(true) - $this->lastSent);
        if (0 < $sleep) {
            $this->logger->debug(sprintf('Email transport "%s" sleeps for %.2f seconds', __CLASS__, $sleep));
            usleep($sleep * 1000000);
        }
        $this->lastSent = microtime(true);
    }
}
