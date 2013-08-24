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
use Symfony\Component\DependencyInjection\Dumper\XmlDumper;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Dumps the ContainerBuilder to a cache file so that it can be used by
 * debugging tools such as the container:debug console command.
 *
 * @author Ryan Weaver <ryan@thatsquality.com>
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @since v2.0.0
 */
class ContainerBuilderDebugDumpPass implements CompilerPassInterface
{
    /**
     * @since v2.0.0
     */
    public function process(ContainerBuilder $container)
    {
        $dumper = new XmlDumper($container);
        $filesystem = new Filesystem();
        $filesystem->dumpFile(
            $container->getParameter('debug.container.dump'),
            $dumper->dump(),
            0666 & ~umask()
        );
    }
}
