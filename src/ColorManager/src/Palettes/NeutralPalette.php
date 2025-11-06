<?php

declare(strict_types=1);

namespace MoonShine\ColorManager\Palettes;

use MoonShine\Contracts\ColorManager\PaletteContract;

final class NeutralPalette implements PaletteContract
{
    public function getDescription(): string
    {
        return 'Black/White';
    }

    public function getColors(): array
    {
        return [
            'body' => '0.99 0 0',
            'primary' => '0.25 0 0',
            'primary-text' => '1 0 0',
            'secondary' => '0.6 0 0',
            'secondary-text' => '1 0 0',
            'base' => [
                'text' => '0.25 0 0',
                'stroke' => '0.25 0 0 / 20%',
                'default' => '1 0 0',
                50 => '0.985 0 0',
                100 => '0.97 0 0',
                200 => '0.955 0 0',
                300 => '0.94 0 0',
                400 => '0.925 0 0',
                500 => '0.91 0 0',
                600 => '0.88 0 0',
                700 => '0.85 0 0',
                800 => '0.80 0 0',
                900 => '0.75 0 0',
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
            'body' => '0.20 0 0',
            'primary' => '1 0 0',
            'primary-text' => '0 0 0',
            'secondary' => '0.8 0 0',
            'secondary-text' => '0.20 0 0',
            'base' => [
                'text' => '0.90 0 0',
                'stroke' => '0.85 0 0 / 20%',
                'default' => '0.24 0 0',
                50 => '0.26 0 0',
                100 => '0.28 0 0',
                200 => '0.32 0 0',
                300 => '0.38 0 0',
                400 => '0.45 0 0',
                500 => '0.52 0 0',
                600 => '0.60 0 0',
                700 => '0.70 0 0',
                800 => '0.80 0 0',
                900 => '0.88 0 0',
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
