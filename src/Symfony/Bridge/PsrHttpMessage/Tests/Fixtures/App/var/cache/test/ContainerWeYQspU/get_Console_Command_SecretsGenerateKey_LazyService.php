<?php

namespace ContainerWeYQspU;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class get_Console_Command_SecretsGenerateKey_LazyService extends Symfony_Bridge_PsrHttpMessage_Tests_Fixtures_App_KernelTestDebugContainer
{
    /**
     * Gets the private '.console.command.secrets_generate_key.lazy' shared service.
     *
     * @return \Symfony\Component\Console\Command\LazyCommand
     */
    public static function do($container, $lazyLoad = true)
    {
        return $container->privates['.console.command.secrets_generate_key.lazy'] = new \Symfony\Component\Console\Command\LazyCommand('secrets:generate-keys', [], 'Generate new encryption keys', false, #[\Closure(name: 'console.command.secrets_generate_key', class: 'Symfony\\Bundle\\FrameworkBundle\\Command\\SecretsGenerateKeysCommand')] fn (): \Symfony\Bundle\FrameworkBundle\Command\SecretsGenerateKeysCommand => ($container->privates['console.command.secrets_generate_key'] ?? $container->load('getConsole_Command_SecretsGenerateKeyService')));
    }
}
