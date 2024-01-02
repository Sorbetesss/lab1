<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bridge\Monolog\Command;

use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\HandlerInterface;
use Monolog\Level;
use Monolog\LogRecord;
use Symfony\Bridge\Monolog\Formatter\ConsoleFormatter;
use Symfony\Bridge\Monolog\Handler\ConsoleHandler;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
#[AsCommand(name: 'server:log', description: 'Start a log server that displays logs in real time')]
class ServerLogCommand extends Command
{
    private const BG_COLOR = ['black', 'blue', 'cyan', 'green', 'magenta', 'red', 'white', 'yellow'];

    private HandlerInterface $handler;

    public function isEnabled(): bool
    {
        if (!class_exists(ConsoleFormatter::class)) {
            return false;
        }

        // based on a symfony/symfony package, it crashes due a missing FormatterInterface from monolog/monolog
        if (!interface_exists(FormatterInterface::class)) {
            return false;
        }

        return parent::isEnabled();
    }

    protected function configure(): void
    {
        if (!class_exists(ConsoleFormatter::class)) {
            return;
        }

        $this
            ->addOption('host', null, InputOption::VALUE_REQUIRED, 'The server host', '0.0.0.0:9911')
            ->addOption('format', null, InputOption::VALUE_REQUIRED, 'The line format', ConsoleFormatter::SIMPLE_FORMAT)
            ->addOption('date-format', null, InputOption::VALUE_REQUIRED, 'The date format', ConsoleFormatter::SIMPLE_DATE)
            ->addOption('filter', null, InputOption::VALUE_REQUIRED, 'An expression to filter log. Example: "level > 200 or channel in [\'app\', \'doctrine\']"')
            ->setHelp(<<<'EOF'
<info>%command.name%</info> starts a log server to display in real time the log
messages generated by your application:

  <info>php %command.full_name%</info>

To filter the log messages using any ExpressionLanguage compatible expression, use the <comment>--filter</> option:

<info>php %command.full_name% --filter="level > 200 or channel in ['app', 'doctrine']"</info>
EOF
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $el = null;
        $filter = $input->getOption('filter');
        if ($filter) {
            if (!class_exists(ExpressionLanguage::class)) {
                throw new LogicException('Package "symfony/expression-language" is required to use the "filter" option. Try running "composer require symfony/expression-language".');
            }
            $el = new ExpressionLanguage();
        }

        $this->handler = new ConsoleHandler($output, true, [
            OutputInterface::VERBOSITY_NORMAL => Level::Debug,
        ]);

        $this->handler->setFormatter(new ConsoleFormatter([
            'format' => str_replace('\n', "\n", $input->getOption('format')),
            'date_format' => $input->getOption('date-format'),
            'colors' => $output->isDecorated(),
            'multiline' => OutputInterface::VERBOSITY_DEBUG <= $output->getVerbosity(),
        ]));

        if (!str_contains($host = $input->getOption('host'), '://')) {
            $host = 'tcp://'.$host;
        }

        $streamHelper = $this->getHelper('stream');
        $streamHelper->listen(
            $input,
            $output,
            $host,
            function (int $clientId, string $message) use ($el, $filter, $output) {
                $record = unserialize(base64_decode($message));

                // Impossible to decode the message, give up.
                if (false === $record) {
                    return;
                }

                if ($filter && !$el->evaluate($filter, $record)) {
                    return;
                }

                $this->displayLog($output, $clientId, $record);
            }
        );

        return Command::SUCCESS;
    }

    private function displayLog(OutputInterface $output, int $clientId, array $record): void
    {
        if (isset($record['log_id'])) {
            $clientId = unpack('H*', $record['log_id'])[1];
        }
        $logBlock = sprintf('<bg=%s> </>', self::BG_COLOR[$clientId % 8]);
        $output->write($logBlock);

        $record = new LogRecord(
            $record['datetime'],
            $record['channel'],
            Level::fromValue($record['level']),
            $record['message'],
            // We wrap context and extra, because they have been already dumped.
            // So they are instance of Symfony\Component\VarDumper\Cloner\Data
            // But LogRecord expects array
            ['data' => $record['context']],
            ['data' => $record['extra']],
        );

        $this->handler->handle($record);
    }
}
