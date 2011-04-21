<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\FrameworkBundle\Routing;

use Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser;
use Symfony\Component\Config\Loader\DelegatingLoader as BaseDelegatingLoader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

/**
 * DelegatingLoader delegates route loading to other loaders using a loader resolver.
 *
 * This implementation resolves the _controller attribute from the short notation
 * to the fully-qualified form (from a:b:c to class:method).
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class DelegatingLoader extends BaseDelegatingLoader implements ContainerAwareInterface
{
    protected $parser;
    protected $logger;
    protected $container;

    /**
     * Constructor.
     *
     * @param ControllerNameParser    $parser   A ControllerNameParser instance
     * @param LoggerInterface         $logger   A LoggerInterface instance
     * @param LoaderResolverInterface $resolver A LoaderResolverInterface instance
     */
    public function __construct(ControllerNameParser $parser, LoggerInterface $logger = null, LoaderResolverInterface $resolver)
    {
        $this->parser = $parser;
        $this->logger = $logger;

        parent::__construct($resolver);
    }
 
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Loads a resource.
     *
     * @param mixed  $resource A resource
     * @param string $type     The resource type
     *
     * @return RouteCollection A RouteCollection instance
     */
    public function load($resource, $type = null)
    {
        $collection = parent::load($resource, $type);
        $parameterBag = new ParameterBag($this->container->parameters);

        foreach ($collection->all() as $name => $route) {
            if ($controller = $route->getDefault('_controller')) {
                try {
                    $controller = $this->parser->parse($controller);
                } catch (\Exception $e) {
                    // unable to optimize unknown notation
                }

                $route->setDefault('_controller', $controller);
            }
            $route->setPattern($parameterBag->resolveValue($route->getPattern()));
        }

        return $collection;
    }
}
