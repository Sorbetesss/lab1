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
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * @author Marc Morera Merino <yuhu@mmoreram.com>
 * @author Marc Morales Valldepérez <marcmorales83@gmail.com>
 * @author Hamza Amrouche <hamza.simperfit@gmail.com>
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

        if (!$constraint instanceof None) {
            throw new UnexpectedTypeException($constraint, None::class);
        }

        if (!is_iterable($value)) {
            throw new UnexpectedValueException($value, 'array or Traversable');
        }

        $validator = $this->context->getValidator()->inContext($this->context);

        $totalIterations = \count($value) * \count($constraint->constraints);

        foreach ($value as $key => $element) {
            $validator->atPath('['.$key.']')->validate($element, $constraint->constraints);
        }

        $constraintsSuccess = $totalIterations - (int) $this->context->getViolations()->count();

        //We clear all violations as just current Validator should add real Violations
        $violations = $this->context->getViolations();
        // We clear all violations as just current Validator should add real Violations
        foreach ($this->context->getViolations() as $key => $violation) {
            $violations->remove($key);
        }

        if ($constraintsSuccess > 0) {
            $this->context->addViolation($constraint->message);
        }
    }
}
