<?php

namespace Symfony\Component\HttpKernel\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class RegisterArgumentResolversPass implements CompilerPassInterface
{
    private $managerService;
    private $resolverTag;

    public function __construct($managerService = 'argument_resolver.manager', $resolverTag = 'kernel.argument_resolver')
    {
        $this->managerService = $managerService;
        $this->resolverTag = $resolverTag;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition($this->managerService) && !$container->hasAlias($this->managerService)) {
            return;
        }

        $definition = $container->findDefinition($this->managerService);
        $resolvers = array();

        foreach ($container->findTaggedServiceIds($this->resolverTag) as $id => $tags) {
            $priority = isset($tags[0]['priority']) ? $tags[0]['priority'] : 0;
            $resolvers[$priority][] = new Reference($id);
        }

        ksort($resolvers);
        $resolvers = call_user_func_array('array_merge', $resolvers);

        $definition->replaceArgument(1, $resolvers);
    }
}
