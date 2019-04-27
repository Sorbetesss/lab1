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

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\Unique;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * @author Marc Morera Merino <yuhu@mmoreram.com>
 * @author Marc Morales Valldepérez <marcmorales83@gmail.com>
 */
class UniqueTest extends TestCase
{
    /**
     * @expectedException \Symfony\Component\Validator\Exception\InvalidOptionsException
     */
    public function testRejectInvalidFieldsOption()
    {
        new Unique([
            'fields' => 'foo',
        ]);
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\InvalidOptionsException
     */
    public function testRejectNonConstraints()
    {
        new Unique([
            'foo' => 'bar',
        ]);
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\InvalidOptionsException
     */
    public function testRejectValidConstraint()
    {
        new Unique([
            'foo' => new Valid(),
        ]);
    }
}
