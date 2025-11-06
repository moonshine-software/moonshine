<?php

declare(strict_types=1);

namespace MoonShine\ColorManager\Palettes;

use MoonShine\Contracts\ColorManager\PaletteContract;

final class CyanPalette implements PaletteContract
{
    public function getDescription(): string
    {
        return 'True cyan blue-green';
    }

    public function getColors(): array
    {
        return [
            'body' => '0.995 0.009 200.873',
            'primary' => '0.65 0.19 200.873',
            'primary-text' => '0.99 0.009 200.873',
            'secondary' => '0.92 0.07 200.873',
            'secondary-text' => '0.22 0.03 200.873',
            'base' => [
                'text' => '0.22 0.03 200.873',
                'stroke' => '0.65 0.19 200.873 / 20%',
                'default' => '0.995 0.009 200.873',
                50 => '0.984 0.019 200.873',
                100 => '0.97 0.03 200.873',
                200 => '0.94 0.06 200.873',
                300 => '0.90 0.09 200.873',
                400 => '0.85 0.12 200.873',
                500 => '0.78 0.15 200.873',
                600 => '0.71 0.17 200.873',
                700 => '0.65 0.19 200.873',
                800 => '0.55 0.15 200.873',
                900 => '0.45 0.11 200.873',
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
            'body' => '0.18 0.04 200.873',
            'primary' => '0.74 0.16 200.873',
            'primary-text' => '0.16 0.05 200.873',
            'secondary' => '0.51 0.13 200.873',
            'secondary-text' => '0.94 0.04 200.873',
            'base' => [
                'text' => '0.90 0.03 200.873',
                'stroke' => '0.74 0.16 200.873 / 20%',
                'default' => '0.22 0.05 200.873',
                50 => '0.24 0.05 200.873',
                100 => '0.29 0.06 200.873',
                200 => '0.34 0.07 200.873',
                300 => '0.41 0.09 200.873',
                400 => '0.49 0.11 200.873',
                500 => '0.58 0.13 200.873',
                600 => '0.67 0.15 200.873',
                700 => '0.74 0.16 200.873',
                800 => '0.82 0.13 200.873',
                900 => '0.88 0.10 200.873',
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
