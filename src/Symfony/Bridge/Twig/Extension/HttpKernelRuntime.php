<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bridge\Twig\Extension;

use Symfony\Component\HttpKernel\Fragment\FragmentHandler;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

/**
 * Provides integration with the HttpKernel component.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class HttpKernelRuntime
{
    private $handler;

    public function __construct(FragmentHandler $handler)
    {
        $this->handler = $handler;
    }

    /**
     * Renders a fragment.
     *
     * @param string|ControllerReference $uri     A URI as a string or a ControllerReference instance
     * @param array                      $options An array of options
     *
     * @return string The fragment content
     *
     * @see FragmentHandler::render()
     */
    public function renderFragment($uri, $options = array()): string
    {
        $strategy = isset($options['strategy']) ? $options['strategy'] : 'inline';
        unset($options['strategy']);

        return $this->handler->render($uri, $strategy, $options);
    }

    /**
     * Renders a fragment.
     *
     * @param string                     $strategy A strategy name
     * @param string|ControllerReference $uri      A URI as a string or a ControllerReference instance
     * @param array                      $options  An array of options
     *
     * @return string The fragment content
     *
     * @see FragmentHandler::render()
     */
    public function renderFragmentStrategy(string $strategy, $uri, $options = array()): string
    {
        return $this->handler->render($uri, $strategy, $options);
    }
}
