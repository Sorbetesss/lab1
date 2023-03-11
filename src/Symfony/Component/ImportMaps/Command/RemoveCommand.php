<?php

declare(strict_types=1);

namespace Symfony\Component\ImportMaps\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\ImportMaps\Env;
use Symfony\Component\ImportMaps\Provider;

/**
 * @author Kévin Dunglas <kevin@dunglas.dev>
 */
#[AsCommand(name: 'importmap:remove', description: 'Removes JavaScript packages')]
final class RemoveCommand extends AbstractCommand
{
    protected function configure(): void
    {
        parent::configure();

        $this->addArgument('packages', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'The packages to remove');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->importMapManager->remove(
            $input->getArgument('packages'),
            Env::from($input->getOption('js-env')),
            Provider::from($input->getOption('provider')),
        );

        return Command::SUCCESS;
    }
}