<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Cache\Exception;

/**
 * Exception thrown when calling a method with an invalid argument.
 *
 * @author Jean-François Simon <contact@jfsimon.fr>
 */
class InvalidQueryException extends \InvalidArgumentException implements CacheExceptionInterface
{
    public static function wrongType($pattern, $query)
    {
        return new self(sprintf($pattern, is_object($query) ? get_class($query) : gettype($query)));
    }

    public static function unsupported($pattern, $query)
    {
        return new self(sprintf($pattern, var_export($query, true)));
    }
}
