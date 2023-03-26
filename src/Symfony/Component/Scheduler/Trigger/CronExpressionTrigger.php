<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Scheduler\Trigger;

use Cron\CronExpression;
use Symfony\Component\Scheduler\Exception\LogicException;

/**
 * Use cron expressions to describe a periodical trigger.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @experimental
 */
final class CronExpressionTrigger implements TriggerInterface, \Stringable
{
    public function __construct(
        private readonly CronExpression $expression = new CronExpression('* * * * *'),
        private readonly int $randomDelay = 0
    ) {
    }

    public function __toString(): string
    {
        return "cron: {$this->expression->getExpression()}";
    }

    public static function fromSpec(string $expression = '* * * * *', int $randomDelay = 0): self
    {
        if (!class_exists(CronExpression::class)) {
            throw new LogicException(sprintf('You cannot use "%s" as the "cron expression" package is not installed; try running "composer require dragonmantank/cron-expression".', __CLASS__));
        }

        return new self(new CronExpression($expression), $randomDelay);
    }

    public function getNextRunDate(\DateTimeImmutable $run): ?\DateTimeImmutable
    {
        $nextRun = $this->expression->getNextRunDate($run);
        if ($this->randomDelay > 0) {
            $nextRun->add(new \DateInterval('PT'.random_int(0, $this->randomDelay).'S'));
        }

        return \DateTimeImmutable::createFromMutable($nextRun);
    }
}
