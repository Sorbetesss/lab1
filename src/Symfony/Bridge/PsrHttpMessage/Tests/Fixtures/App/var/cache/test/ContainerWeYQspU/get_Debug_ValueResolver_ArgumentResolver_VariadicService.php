<?php

namespace ContainerWeYQspU;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class get_Debug_ValueResolver_ArgumentResolver_VariadicService extends Symfony_Bridge_PsrHttpMessage_Tests_Fixtures_App_KernelTestDebugContainer
{
    /**
     * Gets the private '.debug.value_resolver.argument_resolver.variadic' shared service.
     *
     * @return \Symfony\Component\HttpKernel\Controller\ArgumentResolver\TraceableValueResolver
     */
    public static function do($container, $lazyLoad = true)
    {
        return $container->privates['.debug.value_resolver.argument_resolver.variadic'] = new \Symfony\Component\HttpKernel\Controller\ArgumentResolver\TraceableValueResolver(($container->privates['argument_resolver.variadic'] ??= new \Symfony\Component\HttpKernel\Controller\ArgumentResolver\VariadicValueResolver()), ($container->privates['debug.stopwatch'] ??= new \Symfony\Component\Stopwatch\Stopwatch(true)));
    }
}
