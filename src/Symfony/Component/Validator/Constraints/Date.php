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

@trigger_error('The '.__NAMESPACE__.'\Date class is deprecated since version 3.3 and will be removed in 4.0. Use '.__NAMESPACE__.'\DateTime instead.', E_USER_DEPRECATED);

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @deprecated Since version 3.3, to be removed in 4.0. Use Symfony\Component\Validator\Constraints\DateTimeValidator instead.
 */
class Date extends Constraint
{
    const INVALID_FORMAT_ERROR = '69819696-02ac-4a99-9ff0-14e127c4d1bc';
    const INVALID_DATE_ERROR = '3c184ce5-b31d-4de7-8b76-326da7b2be93';

    protected static $errorNames = array(
        self::INVALID_FORMAT_ERROR => 'INVALID_FORMAT_ERROR',
        self::INVALID_DATE_ERROR => 'INVALID_DATE_ERROR',
    );

    public $message = 'This value is not a valid date.';
}
