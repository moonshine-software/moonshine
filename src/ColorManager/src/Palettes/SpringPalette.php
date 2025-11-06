<?php

declare(strict_types=1);

namespace MoonShine\ColorManager\Palettes;

use MoonShine\Contracts\ColorManager\PaletteContract;

final class SpringPalette implements PaletteContract
{
    public function getDescription(): string
    {
        return 'Fresh pastel mint-green';
    }

    public function getColors(): array
    {
        return [
            'body' => '0.99 0.006 145',
            'primary' => '0.75 0.14 145',
            'primary-text' => '0.25 0.03 145',
            'secondary' => '0.92 0.06 85',
            'secondary-text' => '0.25 0.03 85',
            'base' => [
                'text' => '0.25 0.03 145',
                'stroke' => '0.75 0.14 145 / 20%',
                'default' => '0.99 0.006 145',
                50 => '0.97 0.012 145',
                100 => '0.94 0.025 145',
                200 => '0.91 0.04 145',
                300 => '0.87 0.06 145',
                400 => '0.83 0.09 145',
                500 => '0.79 0.11 145',
                600 => '0.77 0.13 145',
                700 => '0.75 0.14 145',
                800 => '0.65 0.11 145',
                900 => '0.55 0.09 145',
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
            'body' => '0.18 0.04 145',
            'primary' => '0.78 0.12 145',
            'primary-text' => '0.16 0.05 145',
            'secondary' => '0.65 0.10 85',
            'secondary-text' => '0.94 0.03 145',
            'base' => [
                'text' => '0.90 0.03 145',
                'stroke' => '0.78 0.12 145 / 20%',
                'default' => '0.22 0.05 145',
                50 => '0.24 0.05 145',
                100 => '0.29 0.06 145',
                200 => '0.34 0.07 145',
                300 => '0.41 0.08 145',
                400 => '0.49 0.09 145',
                500 => '0.58 0.10 145',
                600 => '0.68 0.11 145',
                700 => '0.78 0.12 145',
                800 => '0.84 0.10 145',
                900 => '0.89 0.08 145',
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
