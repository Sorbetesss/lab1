<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Config\Tests\Fixtures\Builder;

use Symfony\Component\Config\Definition\Builder\AbstractNodeDefinition;
use Symfony\Component\Config\Definition\NodeInterface;
use Symfony\Component\Config\Tests\Fixtures\BarNode;

class BarNodeDefinition extends AbstractNodeDefinition
{
    protected function createNode(): NodeInterface
    {
        return new BarNode($this->name);
    }
}
