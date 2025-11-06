<?php

declare(strict_types=1);

namespace MoonShine\ColorManager\Palettes;

use MoonShine\Contracts\ColorManager\PaletteContract;

final class GreenPalette implements PaletteContract
{
    public function getDescription(): string
    {
        return 'Natural green';
    }

    public function getColors(): array
    {
        return [
            'body' => '0.992 0.009 155.826',
            'primary' => '0.63 0.19 155.826',
            'primary-text' => '0.992 0.009 155.826',
            'secondary' => '0.92 0.06 155.826',
            'secondary-text' => '0.22 0.02 155.826',
            'base' => [
                'text' => '0.22 0.02 155.826',
                'stroke' => '0.63 0.19 155.826 / 20%',
                'default' => '0.992 0.009 155.826',
                50 => '0.982 0.018 155.826',
                100 => '0.96 0.03 155.826',
                200 => '0.93 0.05 155.826',
                300 => '0.90 0.08 155.826',
                400 => '0.86 0.11 155.826',
                500 => '0.78 0.14 155.826',
                600 => '0.70 0.17 155.826',
                700 => '0.63 0.19 155.826',
                800 => '0.53 0.15 155.826',
                900 => '0.43 0.11 155.826',
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
            'body' => '0.18 0.03 155.826',
            'primary' => '0.73 0.16 155.826',
            'primary-text' => '0.16 0.04 155.826',
            'secondary' => '0.49 0.13 155.826',
            'secondary-text' => '0.94 0.03 155.826',
            'base' => [
                'text' => '0.90 0.02 155.826',
                'stroke' => '0.73 0.16 155.826 / 20%',
                'default' => '0.22 0.04 155.826',
                50 => '0.24 0.04 155.826',
                100 => '0.29 0.05 155.826',
                200 => '0.34 0.07 155.826',
                300 => '0.40 0.09 155.826',
                400 => '0.48 0.11 155.826',
                500 => '0.56 0.13 155.826',
                600 => '0.64 0.15 155.826',
                700 => '0.73 0.16 155.826',
                800 => '0.81 0.13 155.826',
                900 => '0.88 0.10 155.826',
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
