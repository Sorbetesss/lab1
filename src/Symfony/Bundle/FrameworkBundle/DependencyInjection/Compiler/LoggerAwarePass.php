<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler;

use Psr\Log\LoggerAwareInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Automatically add setLogger method call to any service that implements Psr\Log\LoggerAwareInterface.
 *
 * @see http://www.php-fig.org/psr/psr-3/
 *
 * @author Gary PEGEOT <garypegeot@gmail.com>
 */
class LoggerAwarePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('logger') && !$container->hasAlias('logger')) {
            return;
        }

        $reference = new Reference('logger');

        foreach (\array_keys($container->findTaggedServiceIds('logger.aware')) as $id) {
            $definition = $container->findDefinition($id);
            $class = $container->getReflectionClass($definition->getClass());

            if (!$definition->hasMethodCall('setLogger') && $class && $class->implementsInterface(LoggerAwareInterface::class)) {
                $definition->addMethodCall('setLogger', array($reference));
            }
        }
    }
}
