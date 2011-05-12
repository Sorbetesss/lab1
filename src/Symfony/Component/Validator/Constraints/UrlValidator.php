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
 * Validates whether a value is a valid URL
 *
 * @author Bernhard Schussek <bernhard.schussek@symfony.com>
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class UrlValidator extends ConstraintValidator
{
    public function isValid($value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            return true;
        }

        if (!is_scalar($value) && !(is_object($value) && method_exists($value, '__toString'))) {
            throw new UnexpectedTypeException($value, 'string');
        }

        $value = (string) $value;
        $valid = false;

        // Check for an IPv6 address in URL
        if ((false !== $firstBracert = strpos($value, '://[')) && false !== $secondBracert = strpos($value, ']')) {
            $ip = substr($value, $firstBracert += 4, $secondBracert - $firstBracert);
            $valid = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);

            if ($valid) {
                // IPv6 is valid, so lets replace it and check the rest of value
                // Thats need to be done as filter_var can't validate URL with IPv6
                $valid = filter_var(str_replace('['.$ip.']', 'example.com', $value), FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED);
            }
        } else {
            $valid = filter_var($value, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED);
        }

        // filter_var don't allow to specify what protocol allowed in URL
        if ($valid) {
            foreach ($constraint->protocols as $protocol) {
                if (stripos($value, $protocol . '://') === 0) {
                    break;
                }

                $valid = false;
            }
        }

        if (!$valid) {
            $this->setMessage($constraint->message, array('{{ value }}' => $value));

            return false;
        }

        return true;
    }
}
