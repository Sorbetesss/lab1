<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Http\Firewall;

use Symfony\Component\Security\Http\AccessMap;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventInterface;

/**
 * ChannelListener switches the HTTP protocol based on the access control
 * configuration.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ChannelListener implements ListenerInterface
{
    private $map;
    private $authenticationEntryPoint;
    private $logger;

    public function __construct(AccessMap $map, AuthenticationEntryPointInterface $authenticationEntryPoint, LoggerInterface $logger = null)
    {
        $this->map = $map;
        $this->authenticationEntryPoint = $authenticationEntryPoint;
        $this->logger = $logger;
    }

    /**
     * Handles channel management.
     *
     * @param EventInterface $event An EventInterface instance
     */
    public function handle(EventInterface $event)
    {
        $request = $event->get('request');

        list($attributes, $channel) = $this->map->getPatterns($request);

        if ('https' === $channel && !$request->isSecure()) {
            if (null !== $this->logger) {
                $this->logger->debug('Redirecting to HTTPS');
            }

            $event->setProcessed();

            return $this->authenticationEntryPoint->start($event, $request);
        }

        if ('http' === $channel && $request->isSecure()) {
            if (null !== $this->logger) {
                $this->logger->debug('Redirecting to HTTP');
            }

            $event->setProcessed();

            return $this->authenticationEntryPoint->start($event, $request);
        }
    }
}
