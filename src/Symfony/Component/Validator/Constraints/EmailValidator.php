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
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

/**
 * @api
 */
class EmailValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param mixed      $value      The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     *
     * @return Boolean Whether or not the value is valid
     *
     * @api
     */
    public function isValid($value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            return true;
        }

        if (!is_scalar($value) && !(is_object($value) && method_exists($value, '__toString'))) {
            throw new UnexpectedTypeException($value, 'string');
        }

        $value = (string) $value;

        if ($constraint->multiple) {
            // Check the separator.
            if (null === $constraint->separator || '' === $constraint->separator) {
                throw new ConstraintDefinitionException('The separator must neither be null nor an empty string.');
            }
            $separator = trim(strval($constraint->separator));
            
            // Split into pieces and validate each part.
            $parts = explode($separator, $value);
            $valid = true;
            foreach ($parts as $part) {
                $part = trim($part);

                if ('' === $part) {
                    $valid = false;
                } else {
                    $valid = $valid && $this->checkAddress($part, $constraint);
                }
            }
        } else {
            // Validate a single address.
            $valid = $this->checkAddress($value, $constraint);
        }

        if (!$valid) {
            if ($constraint->multiple) {
                $this->setMessage($constraint->messageMultiple, array('{{ value }}' => $value));
            } else {
                $this->setMessage($constraint->message, array('{{ value }}' => $value));
            }

            return false;
        }

        return true;
    }

    /**
     * Check a single address for validity.
     *
     * @param mixed      $value      The address that should be checked
     * @param Constraint $constraint The constraint for the validation
     *
     * @return Boolean Whether or not the address is valid
     */
    private function checkAddress($value, Constraint $constraint)
    {
        $valid = filter_var($value, FILTER_VALIDATE_EMAIL);

        if ($valid) {
            $host = substr($value, strpos($value, '@') + 1);

            if (version_compare(PHP_VERSION, '5.3.3', '<') && strpos($host, '.') === false) {
                // Likely not a FQDN, bug in PHP FILTER_VALIDATE_EMAIL prior to PHP 5.3.3
                $valid = false;
            }

            // Check MX records
            if ($valid && $constraint->checkMX) {
                $valid = $this->checkMX($host);
            }
        }

        return $valid;
    }

    /**
     * Check DNS Records for MX type.
     *
     * @param string $host Host name
     *
     * @return Boolean
     */
    private function checkMX($host)
    {
        return checkdnsrr($host, 'MX');
    }

}
