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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Matcher\TraceableUrlMatcher;

/**
 * A console command to test route matching.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class RouterMatchCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        if (!$this->getContainer()->has('router')) {
            return false;
        }
        $router = $this->getContainer()->get('router');
        if (!$router instanceof RouterInterface) {
            return false;
        }

        return parent::isEnabled();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('router:match')
            ->setDefinition(array(
                new InputArgument('path_info', InputArgument::REQUIRED, 'A path info'),
                new InputOption('method', null, InputOption::VALUE_REQUIRED, 'Sets the HTTP method', 'GET'),
                new InputOption('host', null, InputOption::VALUE_REQUIRED, 'Sets the HTTP host', 'localhost'),
            ))
            ->setDescription('Helps debug routes by simulating a path info match')
            ->setHelp(<<<EOF
The <info>%command.name%</info> simulates a path info match:

  <info>php %command.full_name% /foo</info>
  or
  <info>php %command.full_name% /foo --method POST --host symfony.com</info>

EOF
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $router = $this->getContainer()->get('router');
        $context = $router->getContext();
        $context->setMethod($input->getOption('method'));
        $context->setHost($input->getOption('host'));

        $matcher = new TraceableUrlMatcher($router->getRouteCollection(), $context);

        $traces = $matcher->getTraces($input->getArgument('path_info'));

        $matches = false;
        foreach ($traces as $trace) {
            if (TraceableUrlMatcher::ROUTE_ALMOST_MATCHES == $trace['level']) {
                $output->writeln(sprintf('<fg=yellow>Route "%s" almost matches but %s</>', $trace['name'], lcfirst($trace['log'])));
            } elseif (TraceableUrlMatcher::ROUTE_MATCHES == $trace['level']) {
                $output->writeln(sprintf('<fg=green>Route "%s" matches</>', $trace['name']));

                $routerDebugcommand = $this->getApplication()->find('router:debug');
                $output->writeln('');
                $routerDebugcommand->run(new ArrayInput(array('name' => $trace['name'])), $output);

                $matches = true;
            } elseif ($input->getOption('verbose')) {
                $output->writeln(sprintf('Route "%s" does not match: %s', $trace['name'], $trace['log']));
            }
        }

        if (!$matches) {
            $output->writeln('<fg=red>None of the routes matches</>');

            return 1;
        }
    }
}
