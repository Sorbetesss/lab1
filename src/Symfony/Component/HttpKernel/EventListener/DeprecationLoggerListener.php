<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Debug\ErrorHandler;

/**
 * Injects the logger into the ErrorHandler, so that it can log deprecation errors.
 *
 * @author Colin Frei <colin@colinfrei.com>
 */
class DeprecationLoggerListener
{
    private $logger;

    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    public function injectLogger()
    {
        if (null !== $this->logger) {
            ErrorHandler::addLoggerToHandlers($this->logger);
        }
    }
}
