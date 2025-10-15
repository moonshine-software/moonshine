<?php

declare(strict_types=1);

namespace MoonShine\ColorManager\Palettes;

use MoonShine\Contracts\ColorManager\PaletteContract;

final class DefaultPalette implements PaletteContract
{
    public function getColors(): array
    {
        return [
            'primary' => '0.627 0.265 303.9',
            'primary-text' => '1 0 0',
            'secondary' => '0.746 0.16 232.661',
            'secondary-text' => '1 0 0',
            'body' => '0.98 0.0035 67.78',
            'theme' => [
                'body' => '0 0 0',
                'stroke' => '0 0 0 / 10%',
                'default' => '1 0 0',
                50 => '0.99 0 0',
                100 => '0.98 0 0',
                200 => '0.97 0 0',
                300 => '0.96 0 0',
                400 => '0.95 0 0',
                500 => '0.94 0 0',
                600 => '0.93 0 0',
                700 => '0.92 0 0',
                800 => '0.91 0 0',
                900 => '0.90 0 0',
            ],
            'success-bg' => '0.639 0.218 142.495',
            'success-text' => '0.4676 0.1549 142.495',
            'warning-bg' => '0.8088 0.170358 75.3501',
            'warning-text' => '0.5 0.1031 76.1',
            'error-bg' => '0.589 0.214 26.855',
            'error-text' => '0.3706 0.145 26.855',
            'info-bg' => '0.601 0.219 257.63',
            'info-text' => '0.3471 0.1204 257.63',
        ];
    }

    public function getDarkColors(): array
    {
        return [
            'primary' => '0.606 0.25 292.717',
            'primary-text' => '1 0 0',
            'secondary' => '0.746 0.16 232.661',
            'secondary-text' => '1 0 0',
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
            'success-bg' => '0.639 0.218 142.495',
            'success-text' => '0.9308 0.1279 144.46',
            'warning-bg' => '0.898 0.177 96.726',
            'warning-text' => '0.9865 0.0716 107.64',
            'error-bg' => '0.589 0.214 26.855',
            'error-text' => '0.8751 0.0665 18.51',
            'info-bg' => '0.601 0.219 257.63',
            'info-text' => '0.877 0.065 244.38',
        ];
    }
}
