<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Validator\Constraints\Collection;

trigger_error('The Symfony\Component\Validator\Constraints\Collection\Optional was deprecated in version 2.x and will be removed in 3.0. You should use Symfony\Component\Validator\Constraints\Optional.', E_USER_DEPRECATED);

use Symfony\Component\Validator\Constraints\Optional as BaseOptional;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @deprecated Deprecated in 2.3, to be removed in 3.0. Use
 *             {@link \Symfony\Component\Validator\Constraints\Optional} instead.
 */
class Optional extends BaseOptional
{
}
