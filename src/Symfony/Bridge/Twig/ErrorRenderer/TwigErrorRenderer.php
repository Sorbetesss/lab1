<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bridge\Twig\ErrorRenderer;

use Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRendererInterface;
use Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

/**
 * Provides the ability to render custom Twig-based HTML error pages
 * in non-debug mode, otherwise falls back to HtmlErrorRenderer.
 *
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
class TwigErrorRenderer implements ErrorRendererInterface
{
    private $twig;
    private $fallbackErrorRenderer;
    private $debug;

    /**
     * @param bool|callable $debug The debugging mode as a boolean or a callable that should return it
     */
    public function __construct(Environment $twig, HtmlErrorRenderer $fallbackErrorRenderer = null, $debug = false)
    {
        if (!\is_bool($debug) && !\is_callable($debug)) {
            throw new \TypeError(sprintf('Argument 2 passed to %s() must be a boolean or a callable, %s given.', __METHOD__, \is_object($debug) ? \get_class($debug) : \gettype($debug)));
        }

        $this->twig = $twig;
        $this->fallbackErrorRenderer = $fallbackErrorRenderer ?? new HtmlErrorRenderer();
        $this->debug = $debug;
    }

    /**
     * {@inheritdoc}
     */
    public function flatten(\Throwable $exception): FlattenException
    {
        if (!method_exists($this->fallbackErrorRenderer, 'flatten')) {
            trigger_deprecation('symfony/error-handler', '5.1', 'Not implementing the "%s::flatten()" method in "%s" is deprecated.', ErrorRendererInterface::class, get_class($this->fallbackErrorRenderer));

            $exception = $this->fallbackErrorRenderer->render($exception);
        } else {
            $exception = $this->fallbackErrorRenderer->flatten($exception);
        }
        $debug = \is_bool($this->debug) ? $this->debug : ($this->debug)($exception);

        if ($debug || !$template = $this->findTemplate($exception->getStatusCode())) {
            return $exception;
        }

        return $exception->setAsString($this->twig->render($template, [
            'exception' => $exception,
            'status_code' => $exception->getStatusCode(),
            'status_text' => $exception->getStatusText(),
        ]));
    }

    /**
     * {@inheritdoc}
     */
    public function render(\Throwable $exception): FlattenException
    {
        trigger_deprecation('symfony/error-handler', '5.1', 'The "%s" method is deprecated, use "%s" instead.', __METHOD__, 'flatten');

        return $this->flatten($exception);
    }

    public static function isDebug(RequestStack $requestStack, bool $debug): \Closure
    {
        return static function () use ($requestStack, $debug): bool {
            if (!$request = $requestStack->getCurrentRequest()) {
                return $debug;
            }

            return $debug && $request->attributes->getBoolean('showException', true);
        };
    }

    private function findTemplate(int $statusCode): ?string
    {
        $template = sprintf('@Twig/Exception/error%s.html.twig', $statusCode);
        if ($this->twig->getLoader()->exists($template)) {
            return $template;
        }

        $template = '@Twig/Exception/error.html.twig';
        if ($this->twig->getLoader()->exists($template)) {
            return $template;
        }

        return null;
    }
}
