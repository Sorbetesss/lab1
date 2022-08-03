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
use Symfony\Component\Validator\Constraints\Isin;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;

class IsinTest extends TestCase
{
    public function testAttributes()
    {
        $metadata = new ClassMetadata(IsinDummy::class);
        $loader = new AnnotationLoader();
        self::assertTrue($loader->loadClassMetadata($metadata));

        [$bConstraint] = $metadata->properties['b']->getConstraints();
        self::assertSame('myMessage', $bConstraint->message);
        self::assertSame(['Default', 'IsinDummy'], $bConstraint->groups);

        [$cConstraint] = $metadata->properties['c']->getConstraints();
        self::assertSame(['my_group'], $cConstraint->groups);
        self::assertSame('some attached data', $cConstraint->payload);
    }
}

class IsinDummy
{
    #[Isin]
    private $a;

    #[Isin(message: 'myMessage')]
    private $b;

    #[Isin(groups: ['my_group'], payload: 'some attached data')]
    private $c;
}
