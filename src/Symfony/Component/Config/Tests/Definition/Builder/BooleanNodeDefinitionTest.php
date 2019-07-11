<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Config\Tests\Definition\Builder;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\BooleanNodeDefinition;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class BooleanNodeDefinitionTest extends TestCase
{
    public function testSetDeprecated()
    {
        $def = new BooleanNodeDefinition('foo');
        $def->setDeprecated('The "%path%" node is deprecated.');

        $node = $def->getNode();

        $this->assertTrue($node->isDeprecated());
        $this->assertSame('The "foo" node is deprecated.', $node->getDeprecationMessage($node->getName(), $node->getPath()));
    }

    public function testCannotBeEmpty()
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Invalid type for path "foo". Expected boolean, but got NULL.');
        $node = new BooleanNodeDefinition('foo');
        $node->allowEmptyValue();
        $node->cannotBeEmpty();

        $node->getNode()->finalize(null);
    }

    public function testAllowEmptyValue()
    {
        $node = new BooleanNodeDefinition('foo');
        $node->allowEmptyValue();

        $this->assertNull($node->getNode()->finalize(null));
    }
}
