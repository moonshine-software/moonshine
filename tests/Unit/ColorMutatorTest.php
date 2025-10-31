<?php

declare(strict_types=1);

namespace MoonShine\Tests\Unit;

use MoonShine\ColorManager\ColorMutator;
use MoonShine\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(ColorMutator::class)]
#[Group('color-manager')]
final class ColorMutatorTest extends TestCase
{
    #[Test]
    #[DataProvider('toHexProvider')]
    public function toHex(string $value, string $expected = '#f3f3f3'): void
    {
        $this->assertEquals($expected, ColorMutator::toHEX($value));
    }

    public static function toHexProvider(): array
    {
        return [
            ['#f3f3f3'],
            ['#e5e5e5', '#e5e5e5'],
            ['rgb(243,243,243)'],
            ['rgb(243, 243, 243)'],
            ['rgba(243, 243, 243)'],
            ['rgba(243, 243, 243, 0.5)'],
            ['243, 243, 243'],
            ['243, 243, 243, 0.5'],
            ['oklch(96.42% 0.000 0)'],
            ['oklch(0.9642 0 0)'],
            ['oklch(1 0 0 / 10%)', '#ffffff1a'],
            ['bad string', '#000000'],
        ];
    }

    #[Test]
    #[DataProvider('toRgbProvider')]
    public function toRgb(string $value, string $expected = 'rgb(243,243,243)'): void
    {
        $this->assertEquals($expected, ColorMutator::toRGB($value));
    }

    public static function toRgbProvider(): array
    {
        return [
            ['#f3f3f3'],
            ['rgb(243,243,243)'],
            ['rgb(243,243,243)'],
            ['rgb(243, 243, 243)'],
            ['rgba(243, 243, 243)'],
            ['rgba(243, 243, 243, 0.5)', 'rgba(243,243,243,0.50)'],
            ['243, 243, 243'],
            ['243, 243, 243, 0.5', 'rgba(243,243,243,0.50)'],
            ['oklch(96.42% 0.000 0)'],
            ['oklch(0.9642 0 0)'],
            ['oklch(1 0 0 / 10%)', 'rgba(255,255,255,0.10)'],
        ];
    }


    #[Test]
    #[DataProvider('toOklchProvider')]
    public function toOklch(string $value, string $expected = 'oklch(96.42% 0 0)'): void
    {
        $this->assertEquals($expected, ColorMutator::toOKLCH($value));
    }

    public static function toOklchProvider(): array
    {
        return [
            ['#f3f3f3'],
            ['#1e96fc', 'oklch(66.31% 0.18027 250.614)'],
            ['rgb(243,243,243)'],
            ['rgba(243, 243, 243)'],
            ['rgba(243, 243, 243, 0.5)'],
            ['243, 243, 243'],
            ['243, 243, 243, 0.5'],
            ['oklch(96.42% 0.000 89.876)'],
            ['oklch(0.96423 0 89.876)'],
            ['oklch(1 0 0 / 10%)', 'oklch(100.00% 0 0 / 10%)'],
            ['0 0 0 / 10%', 'oklch(0.00% 0 0 / 10%)'],
        ];
    }
}
