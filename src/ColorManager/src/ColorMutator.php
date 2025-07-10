<?php

declare(strict_types=1);

namespace MoonShine\ColorManager;

use InvalidArgumentException;

final class ColorMutator
{
    public static function toHEX(string $value): string
    {
        if (str_contains($value, 'oklch')) {
            $rgb = self::fromOKLCH($value);

            return \sprintf('#%02x%02x%02x', ...$rgb);
        }

        if (str_contains($value, '#')) {
            return $value;
        }

        $rgb = self::fromRgb($value);

        if ($rgb === false) {
            return '#000000';
        }

        return \sprintf('#%02x%02x%02x', ...$rgb);
    }

    public static function toRGB(string $value): string
    {
        if (str_contains($value, 'oklch')) {
            $rgb = self::fromOKLCH($value);

            return \sprintf('rgb(%d,%d,%d)', ...$rgb);
        }

        if (str_contains($value, '#')) {
            $hex = ltrim($value, '#');

            if (\strlen($hex) === 3) {
                $hex = preg_replace('/(.)/', '$1$1', $hex);
            }

            $dec = hexdec((string) $hex);

            return \sprintf(
                'rgb(%d,%d,%d)',
                0xFF & ($dec >> 0x10),
                0xFF & ($dec >> 0x8),
                0xFF & $dec,
            );
        }

        $rgb = self::fromRGB($value);

        if ($rgb === false) {
            return 'rgb(0,0,0)';
        }

        if (isset($rgb[3])) {
            return \sprintf('rgba(%d,%d,%d,%.2f)', $rgb[0], $rgb[1], $rgb[2], (float) $rgb[3]);
        }

        return \sprintf('rgb(%d,%d,%d)', ...$rgb);
    }

    public static function toOKLCH(string $value): string
    {
        $result = static function (float $lightness, float $chroma, float $hue): string {
            $l = number_format($lightness * 100, 2, '.', '');
            $c = rtrim(rtrim(number_format($chroma, 3, '.', ''), '0'), '.');
            $h = rtrim(rtrim(number_format($hue, 3, '.', ''), '0'), '.');

            return "oklch({$l}% {$c} {$h})";
        };

        if (str_starts_with($value, 'oklch(')) {
            if (preg_match('/oklch\(\s*([\d.]+)(%)?\s+([\d.]+)\s+([\d.]+)\)/', $value, $matches)) {
                $lightness = (float) $matches[1];

                if ($matches[2] === '%') {
                    $lightness *= 0.01;
                }

                return $result($lightness, (float) $matches[3], (float) $matches[4]);
            }

            [$red, $green, $blue] = self::fromOKLCH($value);
        } elseif (str_starts_with($value, '#')) {
            $rgb = sscanf($value, '#%02x%02x%02x');

            [$red, $green, $blue] = \is_array($rgb) ? $rgb : [0,0,0];
        } else {
            $rgb = self::fromRGB($value);

            [$red, $green, $blue] = \is_array($rgb) ? $rgb : [0,0,0];
        }

        $red /= 255;
        $green /= 255;
        $blue /= 255;

        $red = $red <= 0.04045 ? $red / 12.92 : (($red + 0.055) / 1.055) ** 2.4;
        $green = $green <= 0.04045 ? $green / 12.92 : (($green + 0.055) / 1.055) ** 2.4;
        $blue = $blue <= 0.04045 ? $blue / 12.92 : (($blue + 0.055) / 1.055) ** 2.4;

        $long = 0.4122214708 * $red + 0.5363325363 * $green + 0.0514459929 * $blue;
        $medium = 0.2119034982 * $red + 0.6806995451 * $green + 0.1073969566 * $blue;
        $short = 0.0883024619 * $red + 0.2817188376 * $green + 0.6299787005 * $blue;

        $longCubeRoot = $long ** (1 / 3);
        $mediumCubeRoot = $medium ** (1 / 3);
        $shortCubeRoot = $short ** (1 / 3);

        $lightness = 0.2104542553 * $longCubeRoot + 0.793617785 * $mediumCubeRoot - 0.0040720468 * $shortCubeRoot;

        $colorOpponentA = 1.9779984951 * $longCubeRoot - 2.428592205 * $mediumCubeRoot + 0.4505937099 * $shortCubeRoot;
        $colorOpponentB = 0.0259040371 * $longCubeRoot + 0.7827717662 * $mediumCubeRoot - 0.808675766 * $shortCubeRoot;

        $chroma = sqrt($colorOpponentA * $colorOpponentA + $colorOpponentB * $colorOpponentB);
        $hue = atan2($colorOpponentB, $colorOpponentA);

        $hue = rad2deg($hue);

        if ($hue < 0) {
            $hue += 360;
        }

        return $result($lightness, $chroma, $hue);
    }

    /**
     * @return list<int|float>|false
     */
    public static function fromRGB(string $value): array|false
    {
        if (preg_match('/rgba?\s*\(([^)]+)\)/i', $value, $matches)) {
            $channels = preg_split('/\s*,\s*/', trim($matches[1]));

            if (\is_array($channels) && \count($channels) >= 3) {
                return array_map('floatval', $channels);
            }
        }

        $channels = preg_split('/[\s,]+/', trim($value));

        if (\is_array($channels) && \count($channels) >= 3) {
            return array_map('floatval', $channels);
        }

        return false;
    }

    /**
     * @return int[]
     */
    public static function fromOKLCH(string $value): array
    {
        preg_match('/oklch\\((.*?)\\)/', $value, $matches);
        $parts = preg_split('/\s+/', trim($matches[1]));

        if ($parts === false || \count($parts) < 3) {
            throw new InvalidArgumentException("Invalid OKLCH format: $value");
        }

        $l = str_ends_with($parts[0], '%')
            ? ((float) rtrim($parts[0], '%')) / 100
            : (float) $parts[0];

        $c = str_ends_with($parts[1], '%')
            ? ((float) rtrim($parts[1], '%')) / 100
            : (float) $parts[1];

        $h = (float) ($parts[2] ?? 0);

        $a = cos(deg2rad($h)) * $c;
        $b = sin(deg2rad($h)) * $c;

        $l_ = $l + 0.3963377774 * $a + 0.2158037573 * $b;
        $m_ = $l - 0.1055613458 * $a - 0.0638541728 * $b;
        $s_ = $l - 0.0894841775 * $a - 1.2914855480 * $b;

        $l_ = $l_ > 0 ? $l_ ** 3 : 0;
        $m_ = $m_ > 0 ? $m_ ** 3 : 0;
        $s_ = $s_ > 0 ? $s_ ** 3 : 0;

        $r = 4.0767416621 * $l_ - 3.3077115913 * $m_ + 0.2309699292 * $s_;
        $g = -1.2684380046 * $l_ + 2.6097574011 * $m_ - 0.3413193965 * $s_;
        $b = -0.0041960863 * $l_ - 0.7034186147 * $m_ + 1.7076147010 * $s_;

        $toSrgb = static fn (float $v): float => $v <= 0.0031308
            ? 12.92 * $v
            : 1.055 * ($v ** (1 / 2.4)) - 0.055;

        return [
            (int) round(max(0, min(1, $toSrgb($r))) * 255),
            (int) round(max(0, min(1, $toSrgb($g))) * 255),
            (int) round(max(0, min(1, $toSrgb($b))) * 255),
        ];
    }
}
