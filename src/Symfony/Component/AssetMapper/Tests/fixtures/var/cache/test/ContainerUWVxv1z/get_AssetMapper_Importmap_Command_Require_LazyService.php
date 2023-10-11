<?php

namespace ContainerUWVxv1z;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class get_AssetMapper_Importmap_Command_Require_LazyService extends Symfony_Component_AssetMapper_Tests_fixtures_AssetMapperTestAppKernelTestDebugContainer
{
    /**
     * Gets the private '.asset_mapper.importmap.command.require.lazy' shared service.
     *
     * @return \Symfony\Component\Console\Command\LazyCommand
     */
    public static function do($container, $lazyLoad = true)
    {
        return $container->privates['.asset_mapper.importmap.command.require.lazy'] = new \Symfony\Component\Console\Command\LazyCommand('importmap:require', [], 'Require JavaScript packages', false, #[\Closure(name: 'asset_mapper.importmap.command.require', class: 'Symfony\\Component\\AssetMapper\\Command\\ImportMapRequireCommand')] fn (): \Symfony\Component\AssetMapper\Command\ImportMapRequireCommand => ($container->privates['asset_mapper.importmap.command.require'] ?? $container->load('getAssetMapper_Importmap_Command_RequireService')));
    }
}
