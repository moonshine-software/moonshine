<?php

declare(strict_types=1);

namespace MoonShine\Utilities;

class AssetManager
{
    protected array $assets = [];

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
            ->filter(
                fn ($asset): int|bool => preg_match('/\.js$/', (string) $asset)
            )
            ->map(
                fn ($asset): string => "<script src='" . asset(
                        $asset
                    ) . "'></script>"
            )->implode(PHP_EOL);
    }

    public function css(): string
    {
        return collect($this->assets)
            ->filter(
                fn ($asset): int|bool => preg_match('/\.css$/', (string) $asset)
            )
            ->map(
                fn ($asset): string => "<link href='" . asset(
                        $asset
                    ) . "' rel='stylesheet'>"
            )->implode(PHP_EOL);
    }
}
