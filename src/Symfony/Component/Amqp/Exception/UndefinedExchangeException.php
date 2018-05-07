<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Amqp\Exception;

/**
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class UndefinedExchangeException extends LogicException implements ExceptionInterface
{
}
