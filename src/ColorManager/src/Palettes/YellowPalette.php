<?php

declare(strict_types=1);

namespace MoonShine\ColorManager\Palettes;

use MoonShine\Contracts\ColorManager\PaletteContract;

final class YellowPalette implements PaletteContract
{
    public function getDescription(): string
    {
        return 'Greenish-yellow';
    }

    public function getColors(): array
    {
        return [
            'body' => '0.995 0.013 91.936',
            'primary' => '0.852 0.199 91.936',
            'primary-text' => '0.22 0.03 91.936',
            'secondary' => '0.92 0.07 91.936',
            'secondary-text' => '0.22 0.03 91.936',
            'base' => [
                'text' => '0.22 0.03 91.936',
                'stroke' => '0.68 0.20 91.936 / 20%',
                'default' => '0.995 0.013 91.936',
                50 => '0.987 0.026 91.936',
                100 => '0.97 0.04 91.936',
                200 => '0.94 0.06 91.936',
                300 => '0.91 0.09 91.936',
                400 => '0.87 0.12 91.936',
                500 => '0.80 0.15 91.936',
                600 => '0.73 0.18 91.936',
                700 => '0.68 0.20 91.936',
                800 => '0.58 0.16 91.936',
                900 => '0.48 0.11 91.936',
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
            'body' => '0.18 0.04 91.936',
            'primary' => '0.852 0.199 91.936',
            'primary-text' => '0.16 0.05 91.936',
            'secondary' => '0.51 0.13 91.936',
            'secondary-text' => '0.94 0.04 91.936',
            'base' => [
                'text' => '0.90 0.03 91.936',
                'stroke' => '0.76 0.17 91.936 / 20%',
                'default' => '0.22 0.05 91.936',
                50 => '0.24 0.05 91.936',
                100 => '0.29 0.06 91.936',
                200 => '0.34 0.07 91.936',
                300 => '0.41 0.09 91.936',
                400 => '0.49 0.11 91.936',
                500 => '0.58 0.13 91.936',
                600 => '0.67 0.15 91.936',
                700 => '0.76 0.17 91.936',
                800 => '0.83 0.14 91.936',
                900 => '0.89 0.10 91.936',
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
