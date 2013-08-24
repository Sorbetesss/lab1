<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\OptionsResolver\Exception;

/**
 * Exception thrown when a required option is missing.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @since v2.1.0
 */
class MissingOptionsException extends \InvalidArgumentException implements ExceptionInterface
{
}
