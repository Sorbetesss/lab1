<?php

namespace ContainerWeYQspU;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getPsrRequestControllerService extends Symfony_Bridge_PsrHttpMessage_Tests_Fixtures_App_KernelTestDebugContainer
{
    /**
     * Gets the public 'Symfony\Bridge\PsrHttpMessage\Tests\Fixtures\App\Controller\PsrRequestController' shared autowired service.
     *
     * @return \Symfony\Bridge\PsrHttpMessage\Tests\Fixtures\App\Controller\PsrRequestController
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/Controller/PsrRequestController.php';

        $a = ($container->privates['nyholm.psr_factory'] ??= new \Nyholm\Psr7\Factory\Psr17Factory());

        return $container->services['Symfony\\Bridge\\PsrHttpMessage\\Tests\\Fixtures\\App\\Controller\\PsrRequestController'] = new \Symfony\Bridge\PsrHttpMessage\Tests\Fixtures\App\Controller\PsrRequestController($a, $a);
    }
}
