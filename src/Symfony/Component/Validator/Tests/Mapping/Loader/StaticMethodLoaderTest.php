<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Validator\Tests\Mapping\Loader;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\Loader\StaticMethodLoader;
use Symfony\Component\Validator\Tests\Fixtures\ConstraintA;

class StaticMethodLoaderTest extends TestCase
{
    private $errorLevel;

    protected function setUp(): void
    {
        $this->errorLevel = error_reporting();
    }

    protected function tearDown(): void
    {
        error_reporting($this->errorLevel);
    }

    public function testLoadClassMetadataReturnsTrueIfSuccessful(): void
    {
        $loader = new StaticMethodLoader('loadMetadata');
        $metadata = new ClassMetadata(__NAMESPACE__.'\StaticLoaderEntity');

        $this->assertTrue($loader->loadClassMetadata($metadata));
    }

    public function testLoadClassMetadataReturnsFalseIfNotSuccessful(): void
    {
        $loader = new StaticMethodLoader('loadMetadata');
        $metadata = new ClassMetadata('\stdClass');

        $this->assertFalse($loader->loadClassMetadata($metadata));
    }

    public function testLoadClassMetadata(): void
    {
        $loader = new StaticMethodLoader('loadMetadata');
        $metadata = new ClassMetadata(__NAMESPACE__.'\StaticLoaderEntity');

        $loader->loadClassMetadata($metadata);

        $this->assertEquals(StaticLoaderEntity::$invokedWith, $metadata);
    }

    public function testLoadClassMetadataDoesNotRepeatLoadWithParentClasses(): void
    {
        $loader = new StaticMethodLoader('loadMetadata');
        $metadata = new ClassMetadata(__NAMESPACE__.'\StaticLoaderDocument');
        $loader->loadClassMetadata($metadata);
        $this->assertCount(0, $metadata->getConstraints());

        $loader = new StaticMethodLoader('loadMetadata');
        $metadata = new ClassMetadata(__NAMESPACE__.'\BaseStaticLoaderDocument');
        $loader->loadClassMetadata($metadata);
        $this->assertCount(1, $metadata->getConstraints());
    }

    public function testLoadClassMetadataIgnoresInterfaces(): void
    {
        $loader = new StaticMethodLoader('loadMetadata');
        $metadata = new ClassMetadata(__NAMESPACE__.'\StaticLoaderInterface');

        $loader->loadClassMetadata($metadata);

        $this->assertCount(0, $metadata->getConstraints());
    }

    public function testLoadClassMetadataInAbstractClasses(): void
    {
        $loader = new StaticMethodLoader('loadMetadata');
        $metadata = new ClassMetadata(__NAMESPACE__.'\AbstractStaticLoader');

        $loader->loadClassMetadata($metadata);

        $this->assertCount(1, $metadata->getConstraints());
    }

    public function testLoadClassMetadataIgnoresAbstractMethods(): void
    {
        // Disable error reporting, as AbstractStaticMethodLoader produces a
        // strict standards error
        error_reporting(0);

        $metadata = new ClassMetadata(__NAMESPACE__.'\AbstractStaticMethodLoader');

        $loader = new StaticMethodLoader('loadMetadata');
        $loader->loadClassMetadata($metadata);

        $this->assertCount(0, $metadata->getConstraints());
    }
}

interface StaticLoaderInterface
{
    public static function loadMetadata(ClassMetadata $metadata): void;
}

abstract class AbstractStaticLoader
{
    public static function loadMetadata(ClassMetadata $metadata): void
    {
        $metadata->addConstraint(new ConstraintA());
    }
}

class StaticLoaderEntity
{
    public static $invokedWith = null;

    public static function loadMetadata(ClassMetadata $metadata): void
    {
        self::$invokedWith = $metadata;
    }
}

class StaticLoaderDocument extends BaseStaticLoaderDocument
{
}

class BaseStaticLoaderDocument
{
    public static function loadMetadata(ClassMetadata $metadata): void
    {
        $metadata->addConstraint(new ConstraintA());
    }
}
