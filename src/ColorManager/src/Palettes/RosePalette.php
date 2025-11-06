<?php

declare(strict_types=1);

namespace MoonShine\ColorManager\Palettes;

use MoonShine\Contracts\ColorManager\PaletteContract;

final class RosePalette implements PaletteContract
{
    public function getDescription(): string
    {
        return 'Warm peachy-rose tones';
    }

    public function getColors(): array
    {
        return [
            'body' => '0.985 0.008 12.422',
            'primary' => '0.58 0.24 12.422',
            'primary-text' => '0.985 0.008 12.422',
            'secondary' => '0.92 0.06 12.422',
            'secondary-text' => '0.22 0.02 12.422',
            'base' => [
                'text' => '0.22 0.02 12.422',
                'stroke' => '0.58 0.24 12.422 / 20%',
                'default' => '0.985 0.008 12.422',
                50 => '0.969 0.015 12.422',
                100 => '0.95 0.025 12.422',
                200 => '0.93 0.045 12.422',
                300 => '0.90 0.07 12.422',
                400 => '0.86 0.11 12.422',
                500 => '0.77 0.16 12.422',
                600 => '0.67 0.20 12.422',
                700 => '0.58 0.24 12.422',
                800 => '0.48 0.19 12.422',
                900 => '0.38 0.14 12.422',
            ],
            'success' => '0.64 0.22 142.49',
            'success-text' => '0.46 0.16 142.49',
            'warning' => '0.75 0.17 75.35',
            'warning-text' => '0.5 0.10 76.10',
            'error' => '0.58 0.21 26.855',
            'error-text' => '0.37 0.145 26.85',
            'info' => '0.60 0.219 257.63',
            'info-text' => '0.35 0.12 257.63',
        ];
    }

    public function getDarkColors(): array
    {
        return [
            'body' => '0.18 0.04 12.422',
            'primary' => '0.72 0.18 12.422',
            'primary-text' => '0.16 0.05 12.422',
            'secondary' => '0.48 0.14 12.422',
            'secondary-text' => '0.94 0.04 12.422',
            'base' => [
                'text' => '0.90 0.03 12.422',
                'stroke' => '0.72 0.18 12.422 / 20%',
                'default' => '0.22 0.05 12.422',
                50 => '0.24 0.05 12.422',
                100 => '0.29 0.06 12.422',
                200 => '0.33 0.07 12.422',
                300 => '0.39 0.09 12.422',
                400 => '0.46 0.12 12.422',
                500 => '0.54 0.15 12.422',
                600 => '0.63 0.17 12.422',
                700 => '0.72 0.18 12.422',
                800 => '0.80 0.15 12.422',
                900 => '0.87 0.12 12.422',
            ],
            'success' => '0.64 0.22 142.495',
            'success-text' => '0.93 0.12 144.46',
            'warning' => '0.9 0.22 92.72',
            'warning-text' => '0.99 0.072 107.64',
            'error' => '0.589 0.214 26.855',
            'error-text' => '0.71 0.24 25.96',
            'info' => '0.6 0.22 257.63',
            'info-text' => '0.88 0.065 244.38',
        ];
    }
}
