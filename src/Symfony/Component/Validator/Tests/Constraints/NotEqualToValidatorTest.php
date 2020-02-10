<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Validator\Tests\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\NotEqualTo;
use Symfony\Component\Validator\Constraints\NotEqualToValidator;

/**
 * @author Daniel Holmes <daniel@danielholmes.org>
 */
class NotEqualToValidatorTest extends AbstractComparisonValidatorTestCase
{
    protected function createValidator()
    {
        return new NotEqualToValidator();
    }

    protected function createConstraint(array $options = null): Constraint
    {
        return new NotEqualTo($options);
    }

    protected function getErrorCode(): ?string
    {
        return NotEqualTo::IS_EQUAL_ERROR;
    }

    /**
     * {@inheritdoc}
     */
    public function provideValidComparisons(): array
    {
        $negativeDateInterval = new \DateInterval('PT1H');
        $negativeDateInterval->invert = 1;

        return [
            [1, 2],
            ['22', '333'],
            [new \DateTime('2001-01-01'), new \DateTime('2000-01-01')],
            [new \DateTime('2001-01-01'), '2000-01-01'],
            [new \DateTime('2001-01-01 UTC'), '2000-01-01 UTC'],
            [new ComparisonTest_Class(6), new ComparisonTest_Class(5)],
            [null, 1],
            ['1 != 2 (string)' => new \DateInterval('PT1H'), '+2 hours'],
            ['1 != 2 (\DateInterval instance)' => new \DateInterval('PT1H'), new \DateInterval('PT2H')],
            ['-1 != -2' => $negativeDateInterval, '-2 hours'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function provideValidComparisonsToPropertyPath(): array
    {
        return [
            [0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function provideInvalidComparisons(): array
    {
        $negativeDateInterval = new \DateInterval('PT1H');
        $negativeDateInterval->invert = 1;

        return [
            [3, '3', 3, '3', 'integer'],
            ['2', '"2"', 2, '2', 'integer'],
            ['a', '"a"', 'a', '"a"', 'string'],
            [new \DateTime('2000-01-01'), 'Jan 1, 2000, 12:00 AM', new \DateTime('2000-01-01'), 'Jan 1, 2000, 12:00 AM', 'DateTime'],
            [new \DateTime('2000-01-01'), 'Jan 1, 2000, 12:00 AM', '2000-01-01', 'Jan 1, 2000, 12:00 AM', 'DateTime'],
            [new \DateTime('2000-01-01 UTC'), 'Jan 1, 2000, 12:00 AM', '2000-01-01 UTC', 'Jan 1, 2000, 12:00 AM', 'DateTime'],
            [new ComparisonTest_Class(5), '5', new ComparisonTest_Class(5), '5', __NAMESPACE__.'\ComparisonTest_Class'],
            ['1 == 1 (string)' => new \DateInterval('PT1H'), '1 hour', '+1 hour', '1 hour', \DateInterval::class],
            ['1 == 1 (\DateInterval instance)' => new \DateInterval('PT1H'), '1 hour', new \DateInterval('PT1H'), '1 hour', \DateInterval::class],
            ['-1 == -1' => $negativeDateInterval, '-1 hour', '-1 hour', '-1 hour', \DateInterval::class],
        ];
    }

    public function provideComparisonsToNullValueAtPropertyPath()
    {
        return [
            [5, '5', true],
        ];
    }
}
