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

use Symfony\Component\Validator\Constraints\None;
use Symfony\Component\Validator\Constraints\NoneValidator;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @author Marc Morera Merino <yuhu@mmoreram.com>
 * @author Marc Morales Valldepérez <marcmorales83@gmail.com>
 * @author Hamza Amrouche <hamza.simperfit@gmail.com>
 */
class NoneValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator()
    {
        return $this->validator = new NoneValidator();
    }

    /**
     * Tear down method.
     */
    protected function tearDown()
    {
        $this->validator = null;
    }

    /**
     * Tests that if null, just valid.
     */
    public function testNullIsValid()
    {
        $this->validator->validate(
            null,
            new None(
                [
                    'constraints' => [
                        new Range(['min' => 10]),
                    ],
                ]
            )
        );
        $this->assertNoViolation();
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testThrowsExceptionIfNotTraversable()
    {
        $this->validator->validate('foo.barbar', new None(new Range(['min' => 4])));
    }

    /**
     * Validates success min.
     *
     * @dataProvider getValidArguments
     */
    public function testNotSuccessValidate($array)
    {
        $constraint1 = new Range(['min' => 8]);
        $constraint2 = new Range(['min' => 9]);

        $this->setValidateValueAssertions($array, $constraint1, $constraint2);

        $this->validator->validate(
            $array,
            new None(
                [
                    'constraints' => [
                        $constraint1,
                        $constraint2,
                    ],
                ]
            ));

        $this->assertCount(1, $this->context->getViolations());
    }

    /**
     * Not validates success min.
     *
     * @dataProvider getValidArguments
     */
    public function testSuccessValidate($array)
    {
        $constraint1 = new Range(['min' => 2]);
        $constraint2 = new Range(['min' => 7]);

        $this->setValidateValueAssertions($array, $constraint1, $constraint2);

        $this->validator->validate(
            $array,
            new None(
                [
                    'constraints' => [
                        $constraint1,
                        $constraint2,
                    ],
                ]
            )
        );
        $this->assertCount(1, $this->context->getViolations());
    }

    /**
     * Adds validateValue assertions.
     */
    protected function setValidateValueAssertions($array, $constraint1, $constraint2)
    {
        $iteration = 0;
        foreach ($array as $key => $value) {
            $this->expectValidateValueAt($iteration++, '['.$key.']', $value, [$constraint1, $constraint2]);
        }
    }

    /**
     * Data provider.
     */
    public function getValidArguments()
    {
        return [
            [[5, 6, 7]],
            [new \ArrayObject([5, 6, 7])],
        ];
    }
}
