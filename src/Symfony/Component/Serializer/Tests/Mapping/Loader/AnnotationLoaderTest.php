<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Serializer\Tests\Mapping\Loader;

use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Mapping\AttributeMetadata;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorMapping;
use Symfony\Component\Serializer\Mapping\ClassMetadata;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Tests\Fixtures\AbstractDummy;
use Symfony\Component\Serializer\Tests\Fixtures\AbstractDummyFirstChild;
use Symfony\Component\Serializer\Tests\Fixtures\AbstractDummySecondChild;
use Symfony\Component\Serializer\Tests\Fixtures\AbstractDummyThirdChild;
use Symfony\Component\Serializer\Tests\Fixtures\IgnoreDummy;
use Symfony\Component\Serializer\Tests\Mapping\TestClassMetadataFactory;

/**
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class AnnotationLoaderTest extends TestCase
{
    /**
     * @var AnnotationLoader
     */
    private $loader;

    protected function setUp(): void
    {
        $this->loader = new AnnotationLoader(new AnnotationReader());
    }

    public function testInterface()
    {
        $this->assertInstanceOf('Symfony\Component\Serializer\Mapping\Loader\LoaderInterface', $this->loader);
    }

    public function testLoadClassMetadataReturnsTrueIfSuccessful()
    {
        $classMetadata = new ClassMetadata('Symfony\Component\Serializer\Tests\Fixtures\GroupDummy');

        $this->assertTrue($this->loader->loadClassMetadata($classMetadata));
    }

    public function testLoadGroups()
    {
        $classMetadata = new ClassMetadata('Symfony\Component\Serializer\Tests\Fixtures\GroupDummy');
        $this->loader->loadClassMetadata($classMetadata);

        $this->assertEquals(TestClassMetadataFactory::createClassMetadata(), $classMetadata);
    }

    public function testLoadDiscriminatorMap()
    {
        $classMetadata = new ClassMetadata(AbstractDummy::class);
        $this->loader->loadClassMetadata($classMetadata);

        $expected = new ClassMetadata(AbstractDummy::class, new ClassDiscriminatorMapping('type', [
            'first' => AbstractDummyFirstChild::class,
            'second' => AbstractDummySecondChild::class,
            'third' => AbstractDummyThirdChild::class,
        ]));

        $expected->addAttributeMetadata(new AttributeMetadata('foo'));
        $expected->getReflectionClass();

        $this->assertEquals($expected, $classMetadata);
    }

    public function testLoadMaxDepth()
    {
        $classMetadata = new ClassMetadata('Symfony\Component\Serializer\Tests\Fixtures\MaxDepthDummy');
        $this->loader->loadClassMetadata($classMetadata);

        $attributesMetadata = $classMetadata->getAttributesMetadata();
        $this->assertEquals(2, $attributesMetadata['foo']->getMaxDepth());
        $this->assertEquals(3, $attributesMetadata['bar']->getMaxDepth());
    }

    public function testLoadSerializedName()
    {
        $classMetadata = new ClassMetadata('Symfony\Component\Serializer\Tests\Fixtures\SerializedNameDummy');
        $this->loader->loadClassMetadata($classMetadata);

        $attributesMetadata = $classMetadata->getAttributesMetadata();
        $this->assertEquals(['baz' => []], $attributesMetadata['foo']->getSerializedNames());
        $this->assertEquals(['qux' => []], $attributesMetadata['bar']->getSerializedNames());
    }

    public function testLoadSerializedNames()
    {
        $classMetadata = new ClassMetadata('Symfony\Component\Serializer\Tests\Fixtures\SerializedNameWithGroupsDummy');
        $this->loader->loadClassMetadata($classMetadata);

        $attributesMetadata = $classMetadata->getAttributesMetadata();
        $this->assertEquals(['baz' => []], $attributesMetadata['foo']->getSerializedNames());
        $this->assertEquals(['qux' => []], $attributesMetadata['bar']->getSerializedNames());
        $this->assertEquals(['bargroups' => ['group1']], $attributesMetadata['barWithGroup']->getSerializedNames());
        $this->assertEquals(['quuxgroups2' => ['group1', 'group2'], 'quuxgroups1' => ['group1']], $attributesMetadata['quuxWithGroups']->getSerializedNames());
    }

    public function testLoadClassMetadataAndMerge()
    {
        $classMetadata = new ClassMetadata('Symfony\Component\Serializer\Tests\Fixtures\GroupDummy');
        $parentClassMetadata = new ClassMetadata('Symfony\Component\Serializer\Tests\Fixtures\GroupDummyParent');

        $this->loader->loadClassMetadata($parentClassMetadata);
        $classMetadata->merge($parentClassMetadata);

        $this->loader->loadClassMetadata($classMetadata);

        $this->assertEquals(TestClassMetadataFactory::createClassMetadata(true), $classMetadata);
    }

    public function testLoadIgnore()
    {
        $classMetadata = new ClassMetadata(IgnoreDummy::class);
        $this->loader->loadClassMetadata($classMetadata);

        $attributesMetadata = $classMetadata->getAttributesMetadata();
        $this->assertTrue($attributesMetadata['ignored1']->isIgnored());
        $this->assertTrue($attributesMetadata['ignored2']->isIgnored());
    }
}
