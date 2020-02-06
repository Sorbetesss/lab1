<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\FrameworkBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\YamlDumper;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\AbstractDumper;
use Symfony\Component\VarDumper\Dumper\CliDumper;

/**
 * A console command for autoconfiguration information.
 *
 * @internal
 */
final class DebugAutoconfigurationCommand extends Command
{
    protected static $defaultName = 'debug:autoconfiguration';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDefinition([
                new InputArgument('search', InputArgument::OPTIONAL, 'A search filter'),
                new InputOption('tags', null, InputOption::VALUE_NONE, 'Displays autoconfiguration interfaces/class grouped by tags'),
            ])
            ->setDescription('Displays current autoconfiguration for an application')
            ->setHelp(<<<'EOF'
The <info>%command.name%</info> command displays all services that
are autoconfigured:

  <info>php %command.full_name%</info>

You can also pass a search term to filter the list:

  <info>php %command.full_name% log</info>

EOF
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $errorIo = $io->getErrorStyle();

        $autoconfiguredInstanceofItems = $this->getContainerBuilder()->getAutoconfiguredInstanceof();

        if ($search = $input->getArgument('search')) {
            $autoconfiguredInstanceofItems = array_filter($autoconfiguredInstanceofItems, function ($key) use ($search) {
                return false !== stripos(str_replace('\\', '', $key), $search);
            }, ARRAY_FILTER_USE_KEY);

            if (!$autoconfiguredInstanceofItems) {
                $errorIo->error(sprintf('No autoconfiguration interface/class found matching "%s"', $search));

                return 1;
            }
        }

        ksort($autoconfiguredInstanceofItems, SORT_NATURAL);

        $io->title('Autoconfiguration');
        if ($search) {
            $io->text(sprintf('(only showing classes/interfaces matching <comment>%s</comment>)', $search));
        }
        $io->newLine();

        /** @var ChildDefinition $autoconfiguredInstanceofItem */
        foreach ($autoconfiguredInstanceofItems as $key => $autoconfiguredInstanceofItem) {
            $tableRows = [];

            foreach ($autoconfiguredInstanceofItem->getTags() as $tag => $tagAttributes) {
                $tableRows[] = ['Tag', $tag];
                if ($tagAttributes !== [[]]) {
                    $tableRows[] = ['Tag attribute', $this->dumpTagAttribute($tagAttributes)];
                }
            }

            if ($autoconfiguredInstanceofItem->getMethodCalls()) {
                $tableRows[] = ['Method call', $this->dumpMethodCall($autoconfiguredInstanceofItem)];
            }

            if ($autoconfiguredInstanceofItem->getBindings()) {
                $tableRows[] = ['Bindings', $this->dumpBindings($autoconfiguredInstanceofItem)];
            }

            $io->title(sprintf('Autoconfiguration for "%s"', $key));
            $io->newLine();
            $io->table(['Option', 'Value'], $tableRows);
        }
    }

    private function dumpMethodCall(ChildDefinition $autoconfiguredInstanceofItem)
    {
        $tagContainerBuilder = new ContainerBuilder();
        foreach ($tagContainerBuilder->getServiceIds() as $serviceId) {
            $tagContainerBuilder->removeDefinition($serviceId);
            $tagContainerBuilder->removeAlias($serviceId);
        }
        $tagContainerBuilder->addDefinitions([$autoconfiguredInstanceofItem]);

        $dumper = new YamlDumper($tagContainerBuilder);
        preg_match('/calls\:\n((?: +- .+\n)+)/', $dumper->dump(), $matches);

        return preg_replace('/^\s+/m', '', $matches[1]);
    }

    private function dumpBindings(ChildDefinition $autoconfiguredInstanceofItem)
    {
        $tagContainerBuilder = new ContainerBuilder();
        foreach ($tagContainerBuilder->getServiceIds() as $serviceId) {
            $tagContainerBuilder->removeDefinition($serviceId);
            $tagContainerBuilder->removeAlias($serviceId);
        }

        $dumper = new YamlDumper($tagContainerBuilder);
        foreach ($autoconfiguredInstanceofItem->getBindings() as $bindingKey => $bindingValue) {
            $tagContainerBuilder->setParameter($bindingKey, $bindingValue->getValues()[0]);
        }

        preg_match('/parameters\:\n((?: + .+\n)+)/', $dumper->dump(), $matches);

        return preg_replace('/^\s+/m', '', $matches[1]);
    }

    private function dumpTagAttribute(array $tagAttribute)
    {
        $cloner = new VarCloner();
        $cliDumper = new CliDumper(null, null, AbstractDumper::DUMP_LIGHT_ARRAY);

        return $cliDumper->dump($cloner->cloneVar(current($tagAttribute)), true);
    }

    private function getContainerBuilder(): ContainerBuilder
    {
        $kernel = $this->getApplication()->getKernel();
        $buildContainer = \Closure::bind(function () { return $this->buildContainer(); }, $kernel, \get_class($kernel));
        $container = $buildContainer();
        $container->getCompilerPassConfig()->setRemovingPasses([]);
        $container->getCompilerPassConfig()->setAfterRemovingPasses([]);
        $container->compile();

        return $container;
    }
}
