<?php

declare(strict_types=1);

namespace MoonShine\ColorManager\Palettes;

use MoonShine\Contracts\ColorManager\PaletteContract;

final class SkyPalette implements PaletteContract
{
    public function getDescription(): string
    {
        return 'Sky blue with purple undertone';
    }

    public function getColors(): array
    {
        return [
            'body' => '0.99 0.006 236.62',
            'primary' => '0.60 0.20 236.62',
            'primary-text' => '0.99 0.006 236.62',
            'secondary' => '0.92 0.05 236.62',
            'secondary-text' => '0.22 0.02 236.62',
            'base' => [
                'text' => '0.22 0.02 236.62',
                'stroke' => '0.60 0.20 236.62 / 20%',
                'default' => '0.99 0.006 236.62',
                50 => '0.977 0.013 236.62',
                100 => '0.96 0.022 236.62',
                200 => '0.93 0.04 236.62',
                300 => '0.90 0.07 236.62',
                400 => '0.86 0.10 236.62',
                500 => '0.77 0.14 236.62',
                600 => '0.68 0.17 236.62',
                700 => '0.60 0.20 236.62',
                800 => '0.50 0.16 236.62',
                900 => '0.40 0.12 236.62',
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
            'body' => '0.18 0.03 236.62',
            'primary' => '0.72 0.16 236.62',
            'primary-text' => '0.16 0.04 236.62',
            'secondary' => '0.48 0.13 236.62',
            'secondary-text' => '0.94 0.03 236.62',
            'base' => [
                'text' => '0.90 0.02 236.62',
                'stroke' => '0.72 0.16 236.62 / 20%',
                'default' => '0.22 0.04 236.62',
                50 => '0.24 0.04 236.62',
                100 => '0.29 0.05 236.62',
                200 => '0.33 0.06 236.62',
                300 => '0.39 0.08 236.62',
                400 => '0.46 0.10 236.62',
                500 => '0.54 0.13 236.62',
                600 => '0.63 0.15 236.62',
                700 => '0.72 0.16 236.62',
                800 => '0.80 0.13 236.62',
                900 => '0.87 0.10 236.62',
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
