<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Color;
use Symfony\Component\Console\Exception\InvalidArgumentException;

class ColorTest extends TestCase
{
    public function testAnsiColors()
    {
        $color = new Color();
        $this->assertSame(' ', $color->apply(' '));

        $color = new Color('red', 'yellow');
        $this->assertSame("\033[31;43m \033[39;49m", $color->apply(' '));

        $color = new Color('bright-red', 'bright-yellow');
        $this->assertSame("\033[91;103m \033[39;49m", $color->apply(' '));

        $color = new Color('red', 'yellow', ['underscore']);
        $this->assertSame("\033[31;43;4m \033[39;49;24m", $color->apply(' '));
    }

    public function testTrueColors()
    {
        if ('truecolor' !== getenv('COLORTERM')) {
            $this->markTestSkipped('True color not supported.');
        }

        $color = new Color('#fff', '#000');
        $this->assertSame("\033[38;2;255;255;255;48;2;0;0;0m \033[39;49m", $color->apply(' '));

        $color = new Color('#ffffff', '#000000');
        $this->assertSame("\033[38;2;255;255;255;48;2;0;0;0m \033[39;49m", $color->apply(' '));

        $color = new Color('rgb(255, 255, 255)', 'rgb(0, 0, 0)');
        $this->assertSame("\033[38;2;255;255;255;48;2;0;0;0m \033[39;49m", $color->apply(' '));

        $color = new Color('hsl(0, 100%, 100%)', 'hsl(0, 0%, 0%)');
        $this->assertSame("\033[38;2;255;255;255;48;2;0;0;0m \033[39;49m", $color->apply(' '));
    }

    public function testDegradedTrueColors()
    {
        $colorterm = getenv('COLORTERM');
        putenv('COLORTERM=');

        try {
            $color = new Color('#f00', '#ff0');
            $this->assertSame("\033[31;43m \033[39;49m", $color->apply(' '));

            $color = new Color('#c0392b', '#f1c40f');
            $this->assertSame("\033[31;43m \033[39;49m", $color->apply(' '));

            $color = new Color('rgb(192, 57, 43)', 'rgb(241, 196, 15)');
            $this->assertSame("\033[31;43m \033[39;49m", $color->apply(' '));

            $color = new Color('hsl(6, 63%, 46%)', 'hsl(48, 89%, 50%)');
            $this->assertSame("\033[31;43m \033[39;49m", $color->apply(' '));
        } finally {
            putenv('COLORTERM='.$colorterm);
        }
    }

    /**
     * @dataProvider provideMalformedRgbStrings
     */
    public function testMalformedRgbString(string $color, string $exceptionMessage)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($exceptionMessage);

        new Color($color);
    }

    public function provideMalformedRgbStrings(): \Generator
    {
        yield ['rgb()', 'Invalid RGB functional notation; should be of the form "rgb(r, g, b)", got "rgb()".'];

        yield ['rgb(0, 0)', 'Invalid RGB functional notation; should be of the form "rgb(r, g, b)", got "rgb(0, 0)".'];

        yield ['rgb(0, 0, 0, 0)', 'Invalid RGB functional notation; should be of the form "rgb(r, g, b)", got "rgb(0, 0, 0, 0)".'];

        yield ['rgb(-1, 0, 0)', 'Invalid RGB functional notation; should be of the form "rgb(r, g, b)", got "rgb(-1, 0, 0)".'];

        yield ['rgb(invalid, 0, 0)', 'Invalid RGB functional notation; should be of the form "rgb(r, g, b)", got "rgb(invalid, 0, 0)".'];

        yield ['rgb(256, 0, 0)', 'Invalid color component; value should be between 0 and 255, got 256.'];

        yield ['rgb(0, 0, 0', 'Invalid RGB functional notation; should be of the form "rgb(r, g, b)", got "rgb(0, 0, 0".'];
    }

    /**
     * @dataProvider provideMalformedHslStrings
     */
    public function testMalformedHslString(string $color, string $exceptionMessage)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($exceptionMessage);

        new Color($color);
    }

    public function provideMalformedHslStrings(): \Generator
    {
        yield ['hsl()', 'Invalid HSL functional notation; should be of the form "hsl(h, s%, l%)", got "hsl()".'];

        yield ['hsl(0, 0%)', 'Invalid HSL functional notation; should be of the form "hsl(h, s%, l%)", got "hsl(0, 0%)".'];

        yield ['hsl(0, 0, 0)', 'Invalid HSL functional notation; should be of the form "hsl(h, s%, l%)", got "hsl(0, 0, 0)".'];

        yield ['hsl(0, 0%, 0%, 0%)', 'Invalid HSL functional notation; should be of the form "hsl(h, s%, l%)", got "hsl(0, 0%, 0%, 0%)".'];

        yield ['hsl(360, 0%, 0%)', 'Invalid hue; value should be between 0 and 359, got 360.'];

        yield ['hsl(0, 101%, 0%)', 'Invalid saturation; value should be between 0 and 100, got 101.'];

        yield ['hsl(0, 0%, 101%)', 'Invalid lightness; value should be between 0 and 100, got 101.'];

        yield ['hsl(invalid, 0%, 0%)', 'Invalid HSL functional notation; should be of the form "hsl(h, s%, l%)", got "hsl(invalid, 0%, 0%)".'];

        yield ['hsl(0, 0%, 0%', 'Invalid HSL functional notation; should be of the form "hsl(h, s%, l%)", got "hsl(0, 0%, 0%".'];
    }
}
