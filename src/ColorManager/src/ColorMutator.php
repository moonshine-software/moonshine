<?php

declare(strict_types=1);

namespace MoonShine\ColorManager;

use InvalidArgumentException;

final class ColorMutator
{
    private static function isOKLCHFormat(string $value): bool
    {
        // Check for oklch() format or short format (l c h / alpha)
        return str_contains($value, 'oklch') ||
            preg_match('/^\s*[\d.]+%?\s+[\d.]+%?\s+[\d.]+(?:\s*\/\s*[\d.]+%?)?\s*$/', $value) === 1;
    }

    public static function toHEX(string $value): string
    {
        if (self::isOKLCHFormat($value)) {
            $rgb = self::fromOKLCH($value);

            // Check for alpha channel
            if (isset($rgb[3])) {
                $alpha = (int) round($rgb[3] * 255);

                return \sprintf('#%02x%02x%02x%02x', $rgb[0], $rgb[1], $rgb[2], $alpha);
            }

            return \sprintf('#%02x%02x%02x', $rgb[0], $rgb[1], $rgb[2]);
        }

        if (str_contains($value, '#')) {
            return $value;
        }

        $rgb = self::fromRgb($value);

        if ($rgb === false) {
            return '#000000';
        }

        return \sprintf('#%02x%02x%02x', (int) $rgb[0], (int) $rgb[1], (int) $rgb[2]);
    }

    public static function toRGB(string $value): string
    {
        if (self::isOKLCHFormat($value)) {
            $rgb = self::fromOKLCH($value);

            // Check for alpha channel
            if (isset($rgb[3])) {
                return \sprintf('rgba(%d,%d,%d,%.2f)', $rgb[0], $rgb[1], $rgb[2], (float) $rgb[3]);
            }

            return \sprintf('rgb(%d,%d,%d)', $rgb[0], $rgb[1], $rgb[2]);
        }

        if (str_contains($value, '#')) {
            $hex = ltrim($value, '#');

            if (\strlen($hex) === 3) {
                $hex = preg_replace('/(.)/', '$1$1', $hex);
            }

            // Support for #RRGGBBAA format
            if (\strlen((string) $hex) === 8) {
                $dec = hexdec(substr((string) $hex, 0, 6));
                $alpha = hexdec(substr((string) $hex, 6, 2)) / 255;

                return \sprintf(
                    'rgba(%d,%d,%d,%.2f)',
                    0xFF & ($dec >> 0x10),
                    0xFF & ($dec >> 0x8),
                    0xFF & $dec,
                    $alpha
                );
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
        $formatResult = function (float $lightness, float $chroma, float $hue, ?float $alpha = null): string {
            $l = number_format($lightness * 100, 2, '.', '');
            $c = rtrim(rtrim(number_format($chroma, 5, '.', ''), '0'), '.') ?: '0';
            $h = rtrim(rtrim(number_format($hue, 3, '.', ''), '0'), '.') ?: '0';

            $result = "oklch({$l}% {$c} {$h}";

            if ($alpha !== null) {
                $alphaPercent = number_format($alpha * 100, 0, '.', '');
                $result .= " / {$alphaPercent}%";
            }

            return $result . ')';
        };

        if (preg_match('/^\s*([\d.]+)(%?)\s+([\d.]+)(%?)\s+([\d.]+)(?:\s*\/\s*([\d.]+%?))?\s*$/', $value, $matches)) {
            $lightnessRaw = (float) $matches[1];
            $isPercentL = $matches[2] === '%';
            $chromaRaw = (float) $matches[3];
            $isPercentC = $matches[4] === '%';
            $hueRaw = (float) $matches[5];

            $lightness = $isPercentL ? $lightnessRaw / 100 : $lightnessRaw;
            $chroma = $isPercentC ? $chromaRaw / 100 : $chromaRaw;
            $hue = $chroma === 0.0 ? 0.0 : $hueRaw;

            // Extract alpha if present
            $alpha = null;

            if (isset($matches[6])) {
                $alphaStr = $matches[6];
                $alpha = str_ends_with($alphaStr, '%') ? ((float) rtrim($alphaStr, '%')) / 100 : (float) $alphaStr;
            }

            if ($lightness >= 0 && $lightness <= 1) {
                return $formatResult(
                    max(0, min(1, $lightness)),
                    max(0, $chroma),
                    fmod($hue < 0 ? $hue + 360 : $hue, 360),
                    $alpha
                );
            }
        }

        if (preg_match('/^\s*oklch\(\s*([\d.]+)(%?)\s+([\d.]+)(%?)\s+([\d.]+)(?:\s*\/\s*([\d.]+%?))?\s*\)\s*$/i', $value, $matches)) {
            $lightness = (float) $matches[1];
            if ($matches[2] === '%') {
                $lightness /= 100;
            }

            $chroma = (float) $matches[3];
            if ($matches[4] === '%') {
                $chroma /= 100;
            }

            $hue = $chroma === 0.0 ? 0.0 : (float) $matches[5];

            // Extract alpha if present
            $alpha = null;

            if (isset($matches[6])) {
                $alphaStr = $matches[6];
                $alpha = str_ends_with($alphaStr, '%') ? ((float) rtrim($alphaStr, '%')) / 100 : (float) $alphaStr;
            }

            return $formatResult(
                max(0, min(1, $lightness)),
                max(0, $chroma),
                fmod($hue < 0 ? $hue + 360 : $hue, 360),
                $alpha
            );
        }

        if (str_starts_with($value, '#')) {
            $hex = ltrim($value, '#');

            if (\strlen($hex) === 4) {
                $hex = \sprintf(
                    "#%s%s%s",
                    substr($hex, 0, 1) . substr($hex, 0, 1),
                    substr($hex, 1, 1) . substr($hex, 1, 1),
                    substr($hex, 2, 1) . substr($hex, 2, 1)
                );
            } else {
                $hex = '#' . $hex;
            }

            // Only take first 6 chars (RGB), ignore alpha
            $hex = substr($hex, 0, 7);
            $rgb = sscanf($hex, '#%02x%02x%02x');
            [$red, $green, $blue] = \is_array($rgb) ? $rgb : [0, 0, 0];
        } else {
            $rgb = self::fromRGB($value);
            if ($rgb !== false && \count($rgb) >= 3) {
                [$red, $green, $blue] = [
                    (int) round($rgb[0]),
                    (int) round($rgb[1]),
                    (int) round($rgb[2]),
                ];
            } else {
                [$red, $green, $blue] = [0, 0, 0];
            }
        }

        $red = ($red / 255.0) <= 0.04045 ? ($red / 255.0) / 12.92 : (($red / 255.0 + 0.055) / 1.055) ** 2.4;
        $green = ($green / 255.0) <= 0.04045 ? ($green / 255.0) / 12.92 : (($green / 255.0 + 0.055) / 1.055) ** 2.4;
        $blue = ($blue / 255.0) <= 0.04045 ? ($blue / 255.0) / 12.92 : (($blue / 255.0 + 0.055) / 1.055) ** 2.4;

        $x = $red * 0.4124564 + $green * 0.3575761 + $blue * 0.1804375;
        $y = $red * 0.2126729 + $green * 0.7151522 + $blue * 0.0721750;
        $z = $red * 0.0193339 + $green * 0.1191920 + $blue * 0.9503041;

        // Convert XYZ D65 to LMS using the OKLab M1 matrix
        $l = 0.8189330101 * $x + 0.3618667424 * $y - 0.1288597137 * $z;
        $m = 0.0329845436 * $x + 0.9293118715 * $y + 0.0361456387 * $z;
        $s = 0.0482003018 * $x + 0.2643662691 * $y + 0.6338517070 * $z;

        // Apply cubic root to get LMS'
        $cbrt = static fn (float $v): float => $v >= 0 ? $v ** (1.0 / 3.0) : -((-$v) ** (1.0 / 3.0));
        $l_val = $cbrt($l);
        $m_val = $cbrt($m);
        $s_val = $cbrt($s);

        $L = 0.2104542553 * $l_val + 0.7936177850 * $m_val - 0.0040720468 * $s_val;
        $a = 1.9779984951 * $l_val - 2.4285922050 * $m_val + 0.4505937099 * $s_val;
        $b = 0.0259040371 * $l_val + 0.7827717662 * $m_val - 0.8086757660 * $s_val;

        $C = sqrt($a * $a + $b * $b);

        if ($C < 1e-4) {
            $C = 0.0;
            $H = 0.0;
        } else {
            $H = rad2deg(atan2($b, $a));
            if ($H < 0) {
                $H += 360;
            }
        }

        $L = max(0.0, min(1.0, $L));
        $C = max(0.0, $C);

        return $formatResult($L, $C, $H);
    }

    /**
     * @return array<int|float>|false
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
     * @return array<int|float>
     */
    public static function fromOKLCH(string $value): array
    {
        // Support both oklch() format and short format (l c h / alpha)
        if (str_contains($value, 'oklch(')) {
            preg_match('/oklch\\((.*?)\\)/', $value, $matches);
            $content = trim($matches[1] ?? '');
        } else {
            $content = trim($value);
        }

        // Check for alpha channel (format: "l c h / alpha")
        $alpha = null;
        if (str_contains($content, '/')) {
            [$colorPart, $alphaPart] = array_map('trim', explode('/', $content, 2));
            $content = $colorPart;

            // Parse alpha: can be "10%" or "0.1"
            $alpha = str_ends_with($alphaPart, '%') ? ((float) rtrim($alphaPart, '%')) / 100 : (float) $alphaPart;
        }

        $parts = preg_split('/\s+/', $content);

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

        $result = [
            (int) round(max(0, min(1, $toSrgb($r))) * 255),
            (int) round(max(0, min(1, $toSrgb($g))) * 255),
            (int) round(max(0, min(1, $toSrgb($b))) * 255),
        ];

        // Add alpha channel if present
        if ($alpha !== null) {
            $result[] = $alpha;
        }

        return $result;
    }
}
