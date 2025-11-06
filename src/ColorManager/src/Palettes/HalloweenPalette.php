<?php

declare(strict_types=1);

namespace MoonShine\ColorManager\Palettes;

use MoonShine\Contracts\ColorManager\PaletteContract;

final class HalloweenPalette implements PaletteContract
{
    public function getDescription(): string
    {
        return 'Orange-purple spooky theme';
    }

    public function getColors(): array
    {
        return [
            'body' => '0.98 0.02 285',
            'primary' => '0.65 0.22 40',
            'primary-text' => '1 0.03 40',
            'secondary' => '0.85 0.15 285',
            'secondary-text' => '0.22 0.04 285',
            'base' => [
                'text' => '0.22 0.04 285',
                'stroke' => '0.65 0.22 40 / 20%',
                'default' => '0.98 0.02 285',
                50 => '0.96 0.04 285',
                100 => '0.93 0.07 285',
                200 => '0.90 0.10 285',
                300 => '0.86 0.12 285',
                400 => '0.81 0.14 285',
                500 => '0.75 0.16 40',
                600 => '0.70 0.19 40',
                700 => '0.65 0.22 40',
                800 => '0.55 0.18 40',
                900 => '0.45 0.14 40',
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
            'body' => '0.15 0.04 285',
            'primary' => '0.68 0.20 40',
            'primary-text' => '0.12 0.05 285',
            'secondary' => '0.55 0.18 285',
            'secondary-text' => '0.92 0.06 40',
            'base' => [
                'text' => '0.88 0.05 40',
                'stroke' => '0.68 0.20 40 / 20%',
                'default' => '0.20 0.06 285',
                50 => '0.22 0.06 285',
                100 => '0.27 0.08 285',
                200 => '0.32 0.10 285',
                300 => '0.39 0.12 285',
                400 => '0.47 0.15 285',
                500 => '0.55 0.18 285',
                600 => '0.62 0.19 40',
                700 => '0.68 0.20 40',
                800 => '0.76 0.17 40',
                900 => '0.84 0.13 40',
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
