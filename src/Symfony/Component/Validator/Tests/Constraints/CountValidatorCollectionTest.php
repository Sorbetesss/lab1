<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Validator\Tests\Constraints;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Andreas Linden <linden.andreas@gmx.de>
 */
class CountValidatorCollectionTest extends CountValidatorIterableTest
{
    protected function createCollection(array $content)
    {
        return new ArrayCollection($content);
    }
}
