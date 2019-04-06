<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Loader\DependencyInjection;

use Psr\Container\ContainerInterface;

/**
 * @internal
 *
 * @deprecated since Symfony 4.3, to be removed in 5.0
 */
class ServiceRouterLoaderContainer implements ContainerInterface
{
    private $container;
    private $serviceLocator;

    public function __construct(ContainerInterface $container, ContainerInterface $serviceLocator)
    {
        $this->container = $container;
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        if ($this->serviceLocator->has($id)) {
            return $this->serviceLocator->get($id);
        }

        @trigger_error(sprintf('Registering the routing loader "%s" without tagging it with the "routing.router_loader" tag is deprecated since Symfony 4.3 and will be required in Symfony 5.0.', $id), E_USER_DEPRECATED);

        return $this->container->get($id);
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        return $this->serviceLocator->has($id) || $this->container->has($id);
    }
}
