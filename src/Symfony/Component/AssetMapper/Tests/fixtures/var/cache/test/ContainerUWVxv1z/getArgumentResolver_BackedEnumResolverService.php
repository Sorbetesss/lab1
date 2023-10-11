<?php

namespace ContainerUWVxv1z;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getArgumentResolver_BackedEnumResolverService extends Symfony_Component_AssetMapper_Tests_fixtures_AssetMapperTestAppKernelTestDebugContainer
{
    /**
     * Gets the private 'argument_resolver.backed_enum_resolver' shared service.
     *
     * @return \Symfony\Component\HttpKernel\Controller\ArgumentResolver\BackedEnumValueResolver
     */
    public static function do($container, $lazyLoad = true)
    {
        return $container->privates['argument_resolver.backed_enum_resolver'] = new \Symfony\Component\HttpKernel\Controller\ArgumentResolver\BackedEnumValueResolver();
    }
}
