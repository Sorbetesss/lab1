<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @author Marc Morera Merino <yuhu@mmoreram.com>
 * @author Marc Morales Valldepérez <marcmorales83@gmail.com>
 */
class NoneValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (null === $value) {
            return;
        }

        if (!\is_array($value) && !$value instanceof \Traversable) {
            throw new UnexpectedTypeException($value, 'array or Traversable');
        }

        $group = $this->context->getGroup();

        $totalIterations = \count($value) * \count($constraint->constraints);

        foreach ($value as $key => $element) {
            foreach ($constraint->constraints as $constr) {
                $this->context->validateValue($element, $constr, '['.$key.']', $group);
            }
        }

        $constraintsSuccess = $totalIterations - (int) $this->context->getViolations()->count();

        //We clear all violations as just current Validator should add real Violations
        $this->context->clearViolations();

        if ($constraintsSuccess > 0) {
            $this->context->addViolation($constraint->message);
        }
    }
}
