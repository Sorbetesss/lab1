<?php

namespace ContainerWeYQspU;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class get_Console_Command_CachePoolInvalidateTags_LazyService extends Symfony_Bridge_PsrHttpMessage_Tests_Fixtures_App_KernelTestDebugContainer
{
    /**
     * Gets the private '.console.command.cache_pool_invalidate_tags.lazy' shared service.
     *
     * @return \Symfony\Component\Console\Command\LazyCommand
     */
    public static function do($container, $lazyLoad = true)
    {
        return $container->privates['.console.command.cache_pool_invalidate_tags.lazy'] = new \Symfony\Component\Console\Command\LazyCommand('cache:pool:invalidate-tags', [], 'Invalidate cache tags for all or a specific pool', false, #[\Closure(name: 'console.command.cache_pool_invalidate_tags', class: 'Symfony\\Bundle\\FrameworkBundle\\Command\\CachePoolInvalidateTagsCommand')] fn (): \Symfony\Bundle\FrameworkBundle\Command\CachePoolInvalidateTagsCommand => ($container->privates['console.command.cache_pool_invalidate_tags'] ?? $container->load('getConsole_Command_CachePoolInvalidateTagsService')));
    }
}
