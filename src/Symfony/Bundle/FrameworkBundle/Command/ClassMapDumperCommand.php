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

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\ClassLoader\ClassMapGenerator;

/**
 * ClassMapDumperCommand.
 *
 * @author Luis Cordova <cordoval@gmail.com>
 */
class ClassMapDumperCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setDefinition(array(
                new InputArgument('dir', InputArgument::REQUIRED, 'Directories or a single path to search in.'),
                new InputOption('file', null, InputOption::VALUE_REQUIRED, 'The name of the class map file.', 'classmap.php'),
            ))
            ->setName('generate:class-map')
            ->setDescription('Generates class map file')
            ->setHelp(<<<EOF
The <info>generate:class-map</info> generates class map file.

  <info>generate:class-map dir file</info>
EOF
            )
        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getArgument('dir')) {
            $dir = $input->getArgument('dir');
        }

        if ($input->getOption('file')) {
            $file = $input->getOption('file');
        }

        ClassMapGenerator::dump($dir, $file);

        $output->writeln('Class map has been generated.');
    }
}
