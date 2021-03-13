<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Serializer\Tests\Normalizer;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\StringableNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Tests\Fixtures\JsonSerializableDummy;

/**
 * @author Craig Morris <craig.michael.morris@gmail.com>
 */
class StringableNormalizerTest extends TestCase
{
    /**
     * @var StringableNormalizer
     */
    private $normalizer;

    /**
     * @var MockObject|SerializerInterface
     */
    private $serializer;

    protected function setUp(): void
    {
        $this->createNormalizer();
    }

    private function createNormalizer(array $defaultContext = [])
    {
        $this->serializer = $this->createMock(StringableNormalizer::class);
        $this->normalizer = new StringableNormalizer(null, null, $defaultContext);
        $this->normalizer->setSerializer($this->serializer);
    }

    public function testSupportNormalization()
    {
        $this->assertTrue($this->normalizer->supportsNormalization(new StringableDummy()));
        $this->assertFalse($this->normalizer->supportsNormalization(new \stdClass()));
    }

    public function testNormalize()
    {
        $this->serializer
            ->expects($this->once())
            ->method('normalize')
            ->willReturnCallback(function ($data) {
                $this->assertSame(['foo' => 'a', 'bar' => 'b', 'baz' => 'c'], array_diff_key($data, ['qux' => '']));

                return 'string_object';
            })
        ;

        $this->assertEquals('string_object', $this->normalizer->normalize(new JsonSerializableDummy()));
    }

    public function testCircularNormalize()
    {
        $this->expectException(CircularReferenceException::class);
        $this->createNormalizer([JsonSerializableNormalizer::CIRCULAR_REFERENCE_LIMIT => 1]);

        $this->serializer
            ->expects($this->once())
            ->method('normalize')
            ->willReturnCallback(function ($data, $format, $context) {
                $this->normalizer->normalize($data['qux'], $format, $context);

                return 'string_object';
            })
        ;

        $this->assertEquals('string_object', $this->normalizer->normalize(new JsonSerializableDummy()));
    }

    public function testInvalidDataThrowException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The object must implement "JsonSerializable".');
        $this->normalizer->normalize(new \stdClass());
    }
}

abstract class JsonSerializerNormalizer implements SerializerInterface, NormalizerInterface
{
}
