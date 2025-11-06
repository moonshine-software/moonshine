<?php

declare(strict_types=1);

namespace MoonShine\ColorManager\Palettes;

use MoonShine\Contracts\ColorManager\PaletteContract;

final class WinterPalette implements PaletteContract
{
    public function getDescription(): string
    {
        return 'Cool icy blue tones';
    }

    public function getColors(): array
    {
        return [
            'body' => '0.995 0.004 210',
            'primary' => '0.65 0.16 210',
            'primary-text' => '0.995 0.004 210',
            'secondary' => '0.92 0.03 200',
            'secondary-text' => '0.22 0.02 210',
            'base' => [
                'text' => '0.22 0.02 210',
                'stroke' => '0.65 0.16 210 / 20%',
                'default' => '0.995 0.004 210',
                50 => '0.98 0.008 210',
                100 => '0.96 0.015 210',
                200 => '0.92 0.03 210',
                300 => '0.88 0.05 210',
                400 => '0.82 0.08 210',
                500 => '0.75 0.12 210',
                600 => '0.70 0.14 210',
                700 => '0.65 0.16 210',
                800 => '0.55 0.13 210',
                900 => '0.45 0.10 210',
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
            'body' => '0.17 0.04 210',
            'primary' => '0.72 0.14 210',
            'primary-text' => '0.15 0.05 210',
            'secondary' => '0.50 0.12 200',
            'secondary-text' => '0.94 0.03 210',
            'base' => [
                'text' => '0.92 0.02 210',
                'stroke' => '0.72 0.14 210 / 20%',
                'default' => '0.21 0.05 210',
                50 => '0.23 0.05 210',
                100 => '0.28 0.06 210',
                200 => '0.33 0.07 210',
                300 => '0.40 0.09 210',
                400 => '0.47 0.11 210',
                500 => '0.56 0.12 210',
                600 => '0.64 0.13 210',
                700 => '0.72 0.14 210',
                800 => '0.80 0.12 210',
                900 => '0.88 0.09 210',
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
