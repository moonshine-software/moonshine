<?php

declare(strict_types=1);

namespace MoonShine\ColorManager;

final class ColorMutator
{
    public static function toHEX(string $value): string
    {
        $value = str($value);

        if ($value->contains('#')) {
            return $value->value();
        }

        return $value
            ->explode(',')
            ->map(function ($v): string {
                $v = dechex((int) trim($v));

                if (strlen($v) < 2) {
                    $v = '0' . $v;
                }

                return $v;
            })
            ->prepend('#')
            ->implode('');
    }

    public static function toRGB(string $value): string
    {
        $value = str($value);

        if ($value->contains('#')) {
            $dec = hexdec((string) $value->remove('#')->value());
            $rgb = [
                'red' => 0xFF & ($dec >> 0x10),
                'green' => 0xFF & ($dec >> 0x8),
                'blue' => 0xFF & $dec,
            ];

            return implode(',', $rgb);
        }

        if ($value->contains('rgb')) {
            return $value->remove(['rgb', '(', ')'])
                ->value();
        }

        return $value->value();
    }
}
