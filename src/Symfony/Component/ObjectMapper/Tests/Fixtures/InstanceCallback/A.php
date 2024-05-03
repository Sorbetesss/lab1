<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\ObjectMapper\Tests\Fixtures\InstanceCallback;

use Symfony\Component\ObjectMapper\Attributes\Map;

#[Map(transform: [B::class, 'newInstance'])]
class A
{
    public string $name = 'test';
}