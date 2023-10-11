<?php

namespace ContainerUWVxv1z;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class get_AssetMapper_Importmap_Command_Audit_LazyService extends Symfony_Component_AssetMapper_Tests_fixtures_AssetMapperTestAppKernelTestDebugContainer
{
    /**
     * Gets the private '.asset_mapper.importmap.command.audit.lazy' shared service.
     *
     * @return \Symfony\Component\Console\Command\LazyCommand
     */
    public static function do($container, $lazyLoad = true)
    {
        return $container->privates['.asset_mapper.importmap.command.audit.lazy'] = new \Symfony\Component\Console\Command\LazyCommand('importmap:audit', [], 'Check for security vulnerability advisories for dependencies', false, #[\Closure(name: 'asset_mapper.importmap.command.audit', class: 'Symfony\\Component\\AssetMapper\\Command\\ImportMapAuditCommand')] fn (): \Symfony\Component\AssetMapper\Command\ImportMapAuditCommand => ($container->privates['asset_mapper.importmap.command.audit'] ?? $container->load('getAssetMapper_Importmap_Command_AuditService')));
    }
}
