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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Adds all services with the tags "serializer.encoder" and "serializer.normalizer" as
 * encoders and normalizers to the Serializer service.
 *
 * @author Javier Lopez <f12loalf@gmail.com>
 */
class SerializerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('serializer')) {
            return;
        }

        // Looks for all the services tagged "serializer.normalizer" and adds them to the Serializer service
        $normalizers = $this->findAndSortTaggedServices('serializer.normalizer', $container);
        $container->getDefinition('serializer')->replaceArgument(0, $normalizers);

        // Looks for all the services tagged "serializer.encoders" and adds them to the Serializer service
        $encoders = $this->findAndSortTaggedServices('serializer.encoder', $container);
        $container->getDefinition('serializer')->replaceArgument(1, $encoders);
    }

    private function findAndSortTaggedServices($tagName, ContainerBuilder $container)
    {
        // Find tagged services
        $services = array();
        foreach ($container->findTaggedServiceIds($tagName) as $serviceId => $tags) {
            foreach($tags as $tag) {
                $priority = isset($tag['priority']) ? $tag['priority'] : 0;
                $services[$priority][] = new Reference($serviceId);
            }
        }

        // Sort them
        krsort($services);

        // Flatten the array
        if (!empty($services)) {
            $services = call_user_func_array('array_merge', $services);
        }

        return $services;
    }
}
