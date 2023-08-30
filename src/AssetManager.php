<?php

declare(strict_types=1);

namespace MoonShine;

use Composer\InstalledVersions;
use Illuminate\Support\Traits\Conditionable;

class AssetManager
{
    use Conditionable;

    private array $assets = [];

    private array $colors = [
        'primary' => '120, 67, 233',
        'secondary' => '236, 65, 118',
        'body' => '27, 37, 59',
        'dark' => [
            //'DEFAULT' => '30, 31, 67',
            50 => '83, 103, 132',
            100 => '74, 90, 121',
            200 => '65, 81, 114',
            300 => '53, 69, 103',
            400 => '48, 61, 93',
            500 => '41, 53, 82',
            600 => '40, 51, 78',
            700 => '39, 45, 69',
            800 => '27, 37, 59',
            900 => '15, 23, 42',
        ],

        'success-bg' => '0, 170, 0',
        'success-text' => '255, 255, 255',
        'warning-bg' => '255, 220, 42',
        'warning-text' => '139, 116, 0',
        'error-bg' => '224, 45, 45',
        'error-text' => '255, 255, 255',
        'info-bg' => '0, 121, 255',
        'info-text' => '255, 255, 255',
    ];

    private array $darkColors = [
        'body' => '27, 37, 59',
        'success-bg' => '17, 157, 17',
        'success-text' => '178, 255, 178',
        'warning-bg' => '225, 169, 0',
        'warning-text' => '255, 255, 199',
        'error-bg' => '190, 10, 10',
        'error-text' => '255, 197, 197',
        'info-bg' => '38, 93, 205',
        'info-text' => '179, 220, 255',
    ];

    private string $mainJs = '/vendor/moonshine/js/moonshine.js';

    private string $mainCss = '/vendor/moonshine/css/moonshine.css';

    public function mainCss(string $path): self
    {
        $this->mainCss = $path;

        return $this;
    }

    public function getMainCss(): string
    {
        return $this->mainCss;
    }

    public function getMainJs(): string
    {
        return $this->mainJs;
    }

    public function add(string|array $assets): void
    {
        $this->assets = array_unique(
            array_merge(
                $this->assets,
                is_array($assets) ? $assets : [$assets]
            )
        );
    }

    public function getAssets(): array
    {
        return $this->assets;
    }

    public function js(): string
    {
        return collect($this->assets)
            ->prepend($this->getMainJs())
            ->filter(
                fn ($asset): int|bool => preg_match('/\.js$/', (string) $asset)
            )
            ->map(
                fn ($asset): string => "<script defer src='" . asset(
                    $asset
                ) . "?v={$this->getVersion()}'></script>"
            )->implode(PHP_EOL);
    }

    public function css(): string
    {
        return collect($this->assets)
            ->prepend($this->getMainCss())
            ->filter(
                fn ($asset): int|bool => preg_match('/\.css$/', (string) $asset)
            )
            ->map(
                fn ($asset): string => "<link href='" . asset(
                    $asset
                ) . "?v={$this->getVersion()}' rel='stylesheet'>"
            )->implode(PHP_EOL);
    }

    public function colors(array $colors): static
    {
        foreach ($colors as $name => $color) {
            $this->colors[$name] = $color;
        }

        return $this;
    }

    public function darkColors(array $colors): static
    {
        foreach ($colors as $name => $color) {
            $this->darkColors[$name] = $color;
        }

        return $this;
    }

    public function getColors(bool $dark = false): array
    {
        $colors = [];
        $data = $dark ? $this->darkColors : $this->colors;

        foreach ($data as $name => $shades) {
            if(! is_array($shades)) {
                $colors[$name] = $this->colorToRgb($shades);
            } else {
                foreach ($shades as $shade => $color) {
                    $colors["$name-$shade"] = $this->colorToRgb($color);
                }
            }
        }

        return $colors;
    }

    protected function colorToRgb(string $value): string
    {
        $value = str($value);

        if($value->contains('#')) {
            $dec = hexdec($value->remove('#')->value());
            $rgb = [
                'red'   => 0xFF & ($dec >> 0x10),
                'green' => 0xFF & ($dec >> 0x8),
                'blue'  => 0xFF & $dec
            ];

            return implode(',', $rgb);
        }

        if($value->contains('rgb')) {
            return $value->remove(['rgb', '(', ')'])
                ->value();
        }

        return $value->value();
    }

    public function getVersion(): string
    {
        return InstalledVersions::getVersion('moonshine/moonshine');
    }
}
