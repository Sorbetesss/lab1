<?php

/*
 * This file is part of the Symphony package.
 *
 * (c) Fabien Potencier <fabien@symphony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symphony\Component\Ldap\Exception;

/**
 * ConnectionException is throw if binding to ldap can not be established.
 *
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class ConnectionException extends \RuntimeException implements ExceptionInterface
{
}
