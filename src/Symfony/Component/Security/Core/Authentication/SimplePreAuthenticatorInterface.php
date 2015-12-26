<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Core\Authentication;

use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface as BaseSimplePreAuthenticatorInterface;

/**
 * @deprecated Since version 2.8, to be removed in 3.0. Use the same interface from Security\Http\Authentication instead.
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
interface SimplePreAuthenticatorInterface extends BaseSimplePreAuthenticatorInterface
{
}
