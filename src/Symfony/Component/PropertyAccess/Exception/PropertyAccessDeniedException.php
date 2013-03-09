<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\PropertyAccess\Exception;

/**
 * Thrown when a property cannot be accessed because it is not public.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class PropertyAccessDeniedException extends RuntimeException
{
}
