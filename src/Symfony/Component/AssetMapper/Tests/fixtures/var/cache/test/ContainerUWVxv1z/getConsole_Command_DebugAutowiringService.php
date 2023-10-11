<?php

namespace ContainerUWVxv1z;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getConsole_Command_DebugAutowiringService extends Symfony_Component_AssetMapper_Tests_fixtures_AssetMapperTestAppKernelTestDebugContainer
{
    /**
     * Gets the private 'console.command.debug_autowiring' shared service.
     *
     * @return \Symfony\Bundle\FrameworkBundle\Command\DebugAutowiringCommand
     */
    public static function do($container, $lazyLoad = true)
    {
        $container->privates['console.command.debug_autowiring'] = $instance = new \Symfony\Bundle\FrameworkBundle\Command\DebugAutowiringCommand(NULL, ($container->privates['debug.file_link_formatter'] ??= new \Symfony\Component\HttpKernel\Debug\FileLinkFormatter($container->getEnv('default::SYMFONY_IDE'))));

        $instance->setName('debug:autowiring');
        $instance->setDescription('List classes/interfaces you can use for autowiring');

        return $instance;
    }
}
