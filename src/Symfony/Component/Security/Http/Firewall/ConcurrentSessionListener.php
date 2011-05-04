<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Http\Firewall;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;
use Symfony\Component\Security\Http\Session\SessionRegistry;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

class ConcurrentSessionListener implements ListenerInterface
{
    private $securityContext;
    private $sessionRegistry;
    private $targetUrl;
    private $logger;
    private $handlers;
    private $successHandler;

    public function __construct(SecurityContextInterface $securityContext, SessionRegistry $sessionRegistry, $targetUrl = '/', LogoutSuccessHandlerInterface $successHandler = null, LoggerInterface $logger = null)
    {
        $this->securityContext = $securityContext;
        $this->sessionRegistry = $sessionRegistry;
        $this->targetUrl = $targetUrl;
        $this->successHandler = $successHandler;
        $this->logger = $logger;
        $this->handlers = array();
    }

    /**
     * Adds a logout handler
     *
     * @param LogoutHandlerInterface $handler
     * @return void
     */
    public function addHandler(LogoutHandlerInterface $handler)
    {
        $this->handlers[] = $handler;
    }

    public function register(EventDispatcherInterface $dispatcher)
    {
        $dispatcher->connect('core.security', array($this, 'handle'), 0);
    }

    public function unregister(EventDispatcherInterface $dispatcher)
    {
    }

    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        $session = $request->hasSession() ? $request->getSession() : null;

        if (null !== $session && null !== $token = $this->securityContext->getToken()) {
            if ($sessionInformation = $this->sessionRegistry->getSessionInformation($session->getId())) {
                if ($sessionInformation->isExpired()) {
                    if (null !== $this->successHandler) {
                        $response = $this->successHandler->onLogoutSuccess($request);

                        if (!$response instanceof Response) {
                            throw new \RuntimeException('Logout Success Handler did not return a Response.');
                        }
                    } else {
                        $response = new RedirectResponse(0 !== strpos($this->targetUrl, 'http') ? $request->getUriForPath($this->targetUrl) : $this->targetUrl, 302);
                    }

                    foreach ($this->handlers as $handler) {
                        $handler->logout($request, $response, $token);
                    }

                    $this->securityContext->setToken(null);

                    $event->setResponse($response);
                } else {
                    $sessionInformation->refreshLastRequest();
                    $this->sessionRegistry->setSessionInformation($sessionInformation);
                }
            }
        }

    }
}
