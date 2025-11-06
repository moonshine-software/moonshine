<?php

declare(strict_types=1);

namespace MoonShine\ColorManager\Palettes;

use MoonShine\Contracts\ColorManager\PaletteContract;

final class LimePalette implements PaletteContract
{
    public function getDescription(): string
    {
        return 'Bright lime/chartreuse';
    }

    public function getColors(): array
    {
        return [
            'body' => '0.995 0.015 120.757',
            'primary' => '0.65 0.24 120.757',
            'primary-text' => '0.995 0.015 120.757',
            'secondary' => '0.92 0.08 120.757',
            'secondary-text' => '0.22 0.03 120.757',
            'base' => [
                'text' => '0.22 0.03 120.757',
                'stroke' => '0.65 0.24 120.757 / 20%',
                'default' => '0.995 0.015 120.757',
                50 => '0.986 0.031 120.757',
                100 => '0.97 0.045 120.757',
                200 => '0.94 0.07 120.757',
                300 => '0.91 0.10 120.757',
                400 => '0.87 0.13 120.757',
                500 => '0.80 0.16 120.757',
                600 => '0.72 0.21 120.757',
                700 => '0.65 0.24 120.757',
                800 => '0.55 0.19 120.757',
                900 => '0.45 0.12 120.757',
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
            'body' => '0.18 0.04 120.757',
            'primary' => '0.75 0.19 120.757',
            'primary-text' => '0.16 0.05 120.757',
            'secondary' => '0.50 0.15 120.757',
            'secondary-text' => '0.94 0.04 120.757',
            'base' => [
                'text' => '0.90 0.03 120.757',
                'stroke' => '0.75 0.19 120.757 / 20%',
                'default' => '0.22 0.05 120.757',
                50 => '0.24 0.05 120.757',
                100 => '0.29 0.06 120.757',
                200 => '0.34 0.07 120.757',
                300 => '0.40 0.09 120.757',
                400 => '0.48 0.11 120.757',
                500 => '0.56 0.13 120.757',
                600 => '0.65 0.17 120.757',
                700 => '0.75 0.19 120.757',
                800 => '0.82 0.16 120.757',
                900 => '0.88 0.10 120.757',
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
