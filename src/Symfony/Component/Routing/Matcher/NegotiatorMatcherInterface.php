<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Matcher;

use Symfony\Component\Routing\RequestContextAwareInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

/**
 * NegotiatorMatcherInterface.
 *
 * @author Jean-François Simon <contact@jfsimon.fr>
 */
interface NegotiatorMatcherInterface
{
    /**
     * @return Negotiator
     */
    public function getNegotiator();
}
