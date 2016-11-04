<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Exception;

/**
 * The HTTP request contains host data which is not trusted.
 *
 * @author SpacePossum
 */
final class UntrustedHostException extends AbstractHostException
{
}
