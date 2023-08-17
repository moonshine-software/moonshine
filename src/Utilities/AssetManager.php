<?php

declare(strict_types=1);

namespace MoonShine\Utilities;

use Composer\InstalledVersions;
use Illuminate\Support\Traits\Conditionable;

class AssetManager
{
    use Conditionable;

    private array $assets = [];

    private array $colors = [];

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

    public function getColors(): array
    {
        $colors = [];

        foreach ($this->colors as $name => $shades) {
            if(! is_array($shades)) {
                $colors[$name] = $shades;
            } else {
                foreach ($shades as $shade => $color) {
                    $colors["$name-$shade"] = $color;
                }
            }
        }

        return $colors;
    }

    public function getVersion(): string
    {
        return InstalledVersions::getVersion('moonshine/moonshine');
    }
}
