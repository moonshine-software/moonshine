<?php

declare(strict_types=1);

namespace MoonShine\ColorManager\Palettes;

use MoonShine\Contracts\ColorManager\PaletteContract;

final class OrangePalette implements PaletteContract
{
    public function getDescription(): string
    {
        return 'Classic orange';
    }

    public function getColors(): array
    {
        return [
            'body' => '0.995 0.008 73.684',
            'primary' => '0.70 0.20 73.684',
            'primary-text' => '0.99 0.008 73.684',
            'secondary' => '0.92 0.07 73.684',
            'secondary-text' => '0.22 0.03 73.684',
            'base' => [
                'text' => '0.22 0.03 73.684',
                'stroke' => '0.70 0.20 73.684 / 20%',
                'default' => '0.995 0.008 73.684',
                50 => '0.98 0.016 73.684',
                100 => '0.96 0.03 73.684',
                200 => '0.93 0.06 73.684',
                300 => '0.89 0.09 73.684',
                400 => '0.84 0.13 73.684',
                500 => '0.78 0.16 73.684',
                600 => '0.73 0.18 73.684',
                700 => '0.70 0.20 73.684',
                800 => '0.60 0.16 73.684',
                900 => '0.50 0.12 73.684',
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
            'body' => '0.18 0.04 73.684',
            'primary' => '0.76 0.17 73.684',
            'primary-text' => '0.16 0.05 73.684',
            'secondary' => '0.51 0.13 73.684',
            'secondary-text' => '0.94 0.04 73.684',
            'base' => [
                'text' => '0.90 0.03 73.684',
                'stroke' => '0.76 0.17 73.684 / 20%',
                'default' => '0.22 0.05 73.684',
                50 => '0.24 0.05 73.684',
                100 => '0.29 0.06 73.684',
                200 => '0.34 0.07 73.684',
                300 => '0.41 0.09 73.684',
                400 => '0.49 0.11 73.684',
                500 => '0.58 0.13 73.684',
                600 => '0.67 0.15 73.684',
                700 => '0.76 0.17 73.684',
                800 => '0.83 0.14 73.684',
                900 => '0.89 0.10 73.684',
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
