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
 * @author Hamza Amrouche <hamza.simperfit@gmail.com>
 */
class ExactlyValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (null === $value) {
            return;
        }

        if (!$constraint instanceof Exactly) {
            throw new UnexpectedTypeException($constraint, Exactly::class);
        }

        if (!is_iterable($value)) {
            throw new UnexpectedValueException($value, 'array or Traversable');
        }

        $totalIterations = \count($value) * \count($constraint->constraints);

        $validator = $this->context->getValidator()->inContext($this->context);

        foreach ($value as $key => $element) {
            $validator->atPath('['.$key.']')->validate($element, $constraint->constraints);
        }

        $constraintsSuccess = $totalIterations - (int) $this->context->getViolations()->count();
        $violations = $this->context->getViolations();
        // We clear all violations as just current Validator should add real Violations
        foreach ($this->context->getViolations() as $key => $violation) {
            $violations->remove($key);
        }

        if (isset($constraint->exactly) && $constraintsSuccess != $constraint->exactly) {
            $this->context->buildViolation($constraint->exactlyMessage)
                ->setParameter('{{ limit }}', $constraint->exactly)
                ->addViolation();
        }
    }
}
