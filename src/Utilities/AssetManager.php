<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Utilities;

final class AssetManager
{
    protected static array $assets = [];

    public static function clear(): void
    {
        self::$assets = [];
    }

    public static function add(string|array $assets): void
    {
        self::$assets = array_unique(
            array_merge(
                self::$assets,
                is_array($assets) ? $assets : [$assets]
            )
        );
    }

    public static function getAssets(): array
    {
        return self::$assets;
    }

    public static function js(): string
    {
        return collect(self::$assets)
            ->filter(fn($asset) => preg_match('/\.js$/', $asset))
            ->map(fn($asset) => "<script src='".asset($asset)."'></script>")->implode(PHP_EOL);
    }

    public static function css(): string
    {
        return collect(self::$assets)
            ->filter(fn($asset) => preg_match('/\.css$/', $asset))
            ->map(fn($asset) => "<link href='".asset($asset)."' rel='stylesheet'>")->implode(PHP_EOL);
    }
}
