<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Tests\Component\Uid;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\NilUuid;
use Symfony\Component\Uid\UidFactory;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV1;
use Symfony\Component\Uid\UuidV3;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Uid\UuidV5;
use Symfony\Component\Uid\UuidV6;

class UuidTest extends TestCase
{
    private const A_UUID_V1 = 'd9e7a184-5d5b-11ea-a62a-3499710062d0';
    private const A_UUID_V4 = 'd6b3345b-2905-4048-a83c-b5988e765d98';

    public function testConstructorWithInvalidUuid()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid UUID: "this is not a uuid".');

        Uuid::fromString('this is not a uuid');
    }

    public function testConstructorWithValidUuid()
    {
        $uuid = new UuidV4(self::A_UUID_V4);

        $this->assertSame(self::A_UUID_V4, (string) $uuid);
        $this->assertSame('"'.self::A_UUID_V4.'"', json_encode($uuid));
    }

    public function testV1()
    {
        $uuid = (new UidFactory())->uuidV1();

        $this->assertInstanceOf(UuidV1::class, $uuid);

        $uuid = new UuidV1(self::A_UUID_V1);

        $this->assertSame(1583245966.746458, $uuid->getTime());
        $this->assertSame('3499710062d0', $uuid->getNode());
    }

    public function testV3()
    {
        $uuid = (new UidFactory())->uuidV3(Uuid::fromString(self::A_UUID_V4), 'the name');

        $this->assertInstanceOf(UuidV3::class, $uuid);
    }

    public function testV4()
    {
        $uuid = (new UidFactory())->uuidV4();

        $this->assertInstanceOf(UuidV4::class, $uuid);
    }

    public function testV5()
    {
        $uuid = (new UidFactory())->uuidV5(Uuid::fromString(self::A_UUID_V4), 'the name');

        $this->assertInstanceOf(UuidV5::class, $uuid);
    }

    public function testV6()
    {
        $uuid = (new UidFactory())->uuidV6();

        $this->assertInstanceOf(UuidV6::class, $uuid);

        $uuid = new UuidV6(substr_replace(self::A_UUID_V1, '6', 14, 1));

        $this->assertSame(85916308548.27832, $uuid->getTime());
        $this->assertSame('3499710062d0', $uuid->getNode());
    }

    public function testBinary()
    {
        $uuid = new UuidV4(self::A_UUID_V4);
        $uuid = Uuid::fromString($uuid->toBinary());

        $this->assertInstanceOf(UuidV4::class, $uuid);
        $this->assertSame(self::A_UUID_V4, (string) $uuid);
    }

    public function testFromUlid()
    {
        $ulid = (new UidFactory())->ulid();
        $uuid = Uuid::fromString($ulid);

        $this->assertSame((string) $ulid, $uuid->toBase32());
        $this->assertSame((string) $uuid, $uuid->toRfc4122());
        $this->assertTrue($uuid->equals(Uuid::fromString($ulid)));
    }

    public function testBase58()
    {
        $uuid = new NilUuid();
        $this->assertSame('1111111111111111111111', $uuid->toBase58());

        $uuid = Uuid::fromString("\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF");
        $this->assertSame('YcVfxkQb6JRzqk5kF2tNLv', $uuid->toBase58());
        $this->assertTrue($uuid->equals(Uuid::fromString('YcVfxkQb6JRzqk5kF2tNLv')));
    }

    public function testIsValid()
    {
        $this->assertFalse(Uuid::isValid('not a uuid'));
        $this->assertTrue(Uuid::isValid(self::A_UUID_V4));
        $this->assertFalse(UuidV4::isValid(self::A_UUID_V1));
        $this->assertTrue(UuidV4::isValid(self::A_UUID_V4));
    }

    public function testEquals()
    {
        $uuid1 = new UuidV1(self::A_UUID_V1);
        $uuid2 = new UuidV4(self::A_UUID_V4);

        $this->assertTrue($uuid1->equals($uuid1));
        $this->assertFalse($uuid1->equals($uuid2));
    }

    /**
     * @dataProvider provideInvalidEqualType
     */
    public function testEqualsAgainstOtherType($other)
    {
        $this->assertFalse((new UuidV4(self::A_UUID_V4))->equals($other));
    }

    public function provideInvalidEqualType()
    {
        yield [null];
        yield [self::A_UUID_V1];
        yield [self::A_UUID_V4];
        yield [new \stdClass()];
    }

    public function testCompare()
    {
        $uuids = [];

        $uuids[] = $b = new Uuid('00000000-0000-0000-0000-00000000000b');
        $uuids[] = $a = new Uuid('00000000-0000-0000-0000-00000000000a');
        $uuids[] = $d = new Uuid('00000000-0000-0000-0000-00000000000d');
        $uuids[] = $c = new Uuid('00000000-0000-0000-0000-00000000000c');

        $this->assertNotSame([$a, $b, $c, $d], $uuids);

        usort($uuids, static function (Uuid $a, Uuid $b): int {
            return $a->compare($b);
        });

        $this->assertSame([$a, $b, $c, $d], $uuids);
    }

    public function testNilUuid()
    {
        $uuid = Uuid::fromString('00000000-0000-0000-0000-000000000000');

        $this->assertInstanceOf(NilUuid::class, $uuid);
        $this->assertSame('00000000-0000-0000-0000-000000000000', (string) $uuid);
    }
}
