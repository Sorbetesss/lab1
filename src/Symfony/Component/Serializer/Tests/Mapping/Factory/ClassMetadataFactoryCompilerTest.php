<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Serializer\Tests\Mapping\Factory;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryCompiler;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\Tests\Fixtures\Attributes\MaxDepthDummy;
use Symfony\Component\Serializer\Tests\Fixtures\Attributes\SerializedNameDummy;
use Symfony\Component\Serializer\Tests\Fixtures\Attributes\SerializedPathDummy;
use Symfony\Component\Serializer\Tests\Fixtures\Attributes\SerializedPathInConstructorDummy;
use Symfony\Component\Serializer\Tests\Fixtures\Dummy;

final class ClassMetadataFactoryCompilerTest extends TestCase
{
    private string $dumpPath;

    protected function setUp(): void
    {
        $this->dumpPath = tempnam(sys_get_temp_dir(), 'sf_serializer_metadata_');
    }

    protected function tearDown(): void
    {
        @unlink($this->dumpPath);
    }

    public function testItDumpMetadata()
    {
        $classMetatadataFactory = new ClassMetadataFactory(new AttributeLoader());

        $dummyMetadata = $classMetatadataFactory->getMetadataFor(Dummy::class);
        $maxDepthDummyMetadata = $classMetatadataFactory->getMetadataFor(MaxDepthDummy::class);
        $serializedNameDummyMetadata = $classMetatadataFactory->getMetadataFor(SerializedNameDummy::class);
        $serializedPathDummyMetadata = $classMetatadataFactory->getMetadataFor(SerializedPathDummy::class);
        $serializedPathInConstructorDummyMetadata = $classMetatadataFactory->getMetadataFor(SerializedPathInConstructorDummy::class);

        $code = (new ClassMetadataFactoryCompiler())->compile([
            $dummyMetadata,
            $maxDepthDummyMetadata,
            $serializedNameDummyMetadata,
            $serializedPathDummyMetadata,
            $serializedPathInConstructorDummyMetadata,
        ]);

        file_put_contents($this->dumpPath, $code);
        $compiledMetadata = require $this->dumpPath;

        $this->assertCount(5, $compiledMetadata);

        $this->assertArrayHasKey(Dummy::class, $compiledMetadata);
        $this->assertEquals([
            [
                'foo' => [[], null, [], []],
                'bar' => [[], null, [], []],
                'baz' => [[], null, [], []],
                'qux' => [[], null, [], []],
            ],
            null,
        ], $compiledMetadata[Dummy::class]);

        $this->assertArrayHasKey(MaxDepthDummy::class, $compiledMetadata);
        $this->assertEquals([
            [
                'foo' => [[], 2, [], []],
                'bar' => [[], 3, [], []],
                'child' => [[], null, [], []],
            ],
            null,
        ], $compiledMetadata[MaxDepthDummy::class]);

        $this->assertArrayHasKey(SerializedNameDummy::class, $compiledMetadata);
        $this->assertEquals([
            [
                'foo' => [[], null, ['*' => 'baz'], []],
                'bar' => [[], null, ['*' => 'qux'], []],
                'quux' => [[], null, [], []],
                'duux' => [[], null, ['*' => 'duxi', 'a' => 'duxa'], []],
                'child' => [[], null, [], []],
            ],
            null,
        ], $compiledMetadata[SerializedNameDummy::class]);

        $this->assertArrayHasKey(SerializedPathDummy::class, $compiledMetadata);
        $this->assertEquals([
            [
                'three' => [[], null, [], ['*' => '[one][two]']],
                'seven' => [[], null, [], ['*' => '[three][four]']],
                'eleven' => [[], null, [], ['*' => '[five][six]', 'a' => '[six][five]']],
            ],
            null,
        ], $compiledMetadata[SerializedPathDummy::class]);

        $this->assertArrayHasKey(SerializedPathInConstructorDummy::class, $compiledMetadata);
        $this->assertEquals([
            [
                'three' => [[], null, [], ['*' => '[one][two]']],
                'eleven' => [[], null, [], ['*' => '[five][six]', 'a' => '[six][five]']],
            ],
            null,
        ], $compiledMetadata[SerializedPathInConstructorDummy::class]);
    }
}
