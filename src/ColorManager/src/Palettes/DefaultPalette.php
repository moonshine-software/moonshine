<?php

declare(strict_types=1);

namespace MoonShine\ColorManager\Palettes;

use MoonShine\Contracts\ColorManager\PaletteContract;

final class DefaultPalette implements PaletteContract
{
    public function getColors(): array
    {
        return [
            'primary' => '0 0 0',
            'primary-text' => '1 0 0',
            'secondary' => '0.92 0 0',
            'secondary-text' => '0 0 0',
            'body' => '1 0 0',
            'theme' => [
                'body' => '0 0 0',
                'stroke' => '0 0 0 / 10%',
                'default' => '1 0 0',
                50 => '0.985 0 0',
                100 => '0.97 0 0',
                200 => '0.955 0 0',
                300 => '0.94 0 0',
                400 => '0.925 0 0',
                500 => '0.91 0 0',
                600 => '0.895 0 0',
                700 => '0.88 0 0',
                800 => '0.865 0 0',
                900 => '0.85 0 0',
            ],
            'success-bg' => '0.64 0.22 142.49',
            'success-text' => '0.46 0.16 142.49',
            'warning-bg' => '0.75 0.17 75.35',
            'warning-text' => '0.5 0.10 76.10',
            'error-bg' => '0.58 0.21 26.855',
            'error-text' => '0.37 0.145 26.85',
            'info-bg' => '0.60 0.219 257.63',
            'info-text' => '0.35 0.12 257.63',
        ];
    }

    public function getDarkColors(): array
    {
        return [
            'primary' => '1 0 0',
            'primary-text' => '0 0 0',
            'secondary' => '0.8 0 0',
            'secondary-text' => '0 0 0',
            'body' => '0.2 0.0168 274.32',
            'theme' => [
                'body' => '1 0 0',
                'stroke' => '1 0 0 / 10%',
                'default' => '0.24 0.0168 274.32',
                50 => '0.255 0.017 274.32',
                100 => '0.27 0.017 274.32',
                200 => '0.285 0.018 274.32',
                300 => '0.30 0.019 274.32',
                400 => '0.315 0.020 274.32',
                500 => '0.33 0.021 274.32',
                600 => '0.345 0.022 274.32',
                700 => '0.36 0.023 274.32',
                800 => '0.375 0.024 274.32',
                900 => '0.39 0.025 274.32',
            ],
            'success-bg' => '0.64 0.22 142.495',
            'success-text' => '0.93 0.12 144.46',
            'warning-bg' => '0.9 0.22 92.72',
            'warning-text' => '0.99 0.072 107.64',
            'error-bg' => '0.589 0.214 26.855',
            'error-text' => '0.87 0.07 18.51',
            'info-bg' => '0.6 0.22 257.63',
            'info-text' => '0.88 0.065 244.38',
        ];
    }
}
