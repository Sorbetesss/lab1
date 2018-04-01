<?php

/*
 * This file is part of the Symphony package.
 *
 * (c) Fabien Potencier <fabien@symphony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symphony\Component\PropertyAccess\Tests;

use Symphony\Component\PropertyAccess\Tests\Fixtures\NonTraversableArrayObject;

class PropertyAccessorNonTraversableArrayObjectTest extends PropertyAccessorArrayAccessTest
{
    protected function getContainer(array $array)
    {
        return new NonTraversableArrayObject($array);
    }
}
